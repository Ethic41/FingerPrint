/** 
 * -=-<[ Bismillahirrahmanirrahim ]>-=-
 * Date    : 2021-01-23 10:39:01
 * Author  : Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * About   : Compile with g++
 */

#include <iostream>
#include <string>
#include <vector>
#include <map>
#include <cstdlib>

// DigitalPersona Includes
#include "dpfj.h"

// Base64 Includes
#include "base64.h"

// gRPC Includes
#include <grpc/grpc.h>
#include <grpcpp/server.h>
#include <grpcpp/server_builder.h>
#include <grpcpp/server_context.h>
#include <grpcpp/security/server_credentials.h>
#include "helper.h"
#include "fingerprint.grpc.pb.h"

#define MY_MAX_FMD_SIZE 2000
#define MAX_PORT "65535"
#define DEFAULT_PORT "4134" // DMD :)

using grpc::Server;
using grpc::ServerBuilder;
using grpc::ServerContext;
using grpc::Status;

using fingerprint::PreEnrolledFMD;
using fingerprint::EnrolledFMD;
using fingerprint::EnrollmentRequest;

using fingerprint::VerificationRequest;
using fingerprint::VerificationResponse;

using fingerprint::CheckDuplicateResponse;
using fingerprint::FingerPrint;

using std::vector;
using std::string;
using std::cout;
using std::getenv;
using std::endl;
using std::unique_ptr;
using std::map;

typedef unsigned char MyFMD;
typedef unsigned int MyFMDSize;

class FingerPrintImpl final : public FingerPrint::Service {
    private:
        unsigned int threshold_score = DPFJ_PROBABILITY_ONE / 1000000;

    public:
        Status EnrollFingerprint(ServerContext* context, const EnrollmentRequest* enrollmentRequest, EnrolledFMD* enrolledFMD) {
            //pass
            enrolledFMD->set_base64enrolledfmd(createEnrollment(enrollmentRequest)->base64enrolledfmd());
            return Status::OK;
        }

        EnrolledFMD* createEnrollment(const EnrollmentRequest* enrollmentRequest){
            
            vector<string> pre_reg_fmd_cont = {};  //container for decoded preregistered FMDs

            for (PreEnrolledFMD pre_enrolled_fmd: enrollmentRequest->fmdcandidates()) {
                /* 
                    Decode the received base64 encoded pre_registered FMDs
                    and store them in a container
                 */
                string decoded_pre_reg_fmd = base64_decode(pre_enrolled_fmd.base64preenrolledfmd());
                pre_reg_fmd_cont.push_back(decoded_pre_reg_fmd);

            }

            int start_enrollment_process = dpfj_start_enrollment(DPFJ_FMD_DP_REG_FEATURES);

            if (start_enrollment_process == DPFJ_SUCCESS) {
                cout << "enrollment process started successfully" << endl;

                for (string pre_reg_fmd: pre_reg_fmd_cont) {
                    
                    MyFMD* pre_reg_fmd_ptr = (MyFMD*) pre_reg_fmd.c_str();

                    int enrollment_add_pre_reg_fmd = dpfj_add_to_enrollment(
                        DPFJ_FMD_DP_PRE_REG_FEATURES, 
                        pre_reg_fmd_ptr, 
                        pre_reg_fmd.size(), 0
                    );

                    if (enrollment_add_pre_reg_fmd == DPFJ_E_MORE_DATA) {
                        cout << "adding more pre_registered fmd for enrollment..." << endl;
                        continue;
                    }

                    else if (enrollment_add_pre_reg_fmd == DPFJ_SUCCESS) {
                        cout << "pre_reg fmds added, ready for enrollment..." << endl;
                        
                        MyFMD reg_fmd[MY_MAX_FMD_SIZE];
                        unsigned int fmd_size = MY_MAX_FMD_SIZE;

                        int enrollment_result = dpfj_create_enrollment_fmd(reg_fmd, &fmd_size);

                        EnrolledFMD* enrolled_fmd = new EnrolledFMD();

                        if (enrollment_result == DPFJ_SUCCESS) {
                            cout << "enrollment successful..." << endl;
                            string enrolled_fmd_ptr(reinterpret_cast<char *>(reg_fmd), fmd_size);
                            string encoded_fmd = base64_encode(enrolled_fmd_ptr);

                            enrolled_fmd->set_base64enrolledfmd(encoded_fmd);
                            dpfj_finish_enrollment();
                            return enrolled_fmd;
                        }

                        else if (enrollment_result == DPFJ_E_MORE_DATA) {
                            cout << "insufficient memory in create_enrollment_fmd()..." << endl;
                            dpfj_finish_enrollment();
                            return enrolled_fmd;
                        }

                        else {
                            cout << "Unknown error occurred in create_enrollment_fmd()..." << endl;
                            dpfj_finish_enrollment();
                            return enrolled_fmd;
                        }

                    }

                    else if (enrollment_add_pre_reg_fmd == DPFJ_E_INVALID_PARAMETER) {
                        cout << "one or more parameters are invalid, during add_enrollment()..." << endl;
                        continue;
                    }

                    else {
                        cout << "Unknown error occurred during add_enrollment()..." << endl;
                        dpfj_finish_enrollment();
                        break;
                    }

                }
            }
            else {
                cout << "failed to start enrollment..." << endl;
                dpfj_finish_enrollment();
            }

            return new EnrolledFMD();
        }

        Status VerifyFingerprint(ServerContext* context, const VerificationRequest* verification_request, VerificationResponse* verification_response) {
            //pass
            verification_response->set_match(makeVerification(verification_request)->match());
            return Status::OK;
        }

        VerificationResponse* makeVerification(const VerificationRequest* verification_request){
            
            string target_pre_fmd = base64_decode(verification_request->targetfmd().base64preenrolledfmd());
            MyFMD* target_pre_fmd_ptr = (MyFMD* ) target_pre_fmd.c_str();

            map<MyFMD*, MyFMDSize*> target_verify_fmd_map = convert_fmd(DPFJ_FMD_DP_PRE_REG_FEATURES, DPFJ_FMD_DP_VER_FEATURES, target_pre_fmd_ptr, (MyFMDSize ) target_pre_fmd.size());

            map<MyFMD*, MyFMDSize*>::iterator target_verify_fmd_map_it = target_verify_fmd_map.begin();

            MyFMD* target_verify_fmd_ptr = target_verify_fmd_map_it->first;
            MyFMDSize* target_verify_fmd_size = target_verify_fmd_map_it->second;

            int candidates_count = verification_request->fmdcandidates_size();

            string candidates_container[candidates_count];
            MyFMD* candidates_pointers_container[candidates_count];
            MyFMDSize candidates_size_container[candidates_count];

            DPFJ_CANDIDATE matched_candidates_container[1];
            MyFMDSize expected_matches_count = 1;

            int counter = 0;

            for (EnrolledFMD enrolled_candidate: verification_request->fmdcandidates()) {

                string candidate_reg_fmd = base64_decode(enrolled_candidate.base64enrolledfmd());
                candidates_container[counter] = candidate_reg_fmd;
                candidates_size_container[counter] = candidate_reg_fmd.size();
                candidates_pointers_container[counter] = (MyFMD* ) candidates_container[counter].c_str();
                counter++;

            }

            int identify_result = dpfj_identify(DPFJ_FMD_DP_VER_FEATURES, target_verify_fmd_ptr, *target_verify_fmd_size, 0, DPFJ_FMD_DP_REG_FEATURES, candidates_count, candidates_pointers_container, candidates_size_container, threshold_score, &expected_matches_count, matched_candidates_container);

            VerificationResponse* verification_response = new VerificationResponse();
            verification_response->set_match(false);

            if (identify_result == DPFJ_SUCCESS && expected_matches_count > 0) {
                cout << expected_matches_count << " match found..." << endl;
                verification_response->set_match(true);
                return verification_response;
            }

            else {
                cout << "No match found..." << endl;
                return verification_response;
            }
        }

        Status CheckDuplicate(ServerContext* context, const VerificationRequest* verification_request, CheckDuplicateResponse* check_duplicate_response) {
            if (!makeVerification(verification_request)->match()) {
                check_duplicate_response->set_isduplicate(false);
            }

            else{
                check_duplicate_response->set_isduplicate(true);
            }

            return Status::OK;

        }

        map<MyFMD*, MyFMDSize*> convert_fmd(DPFJ_FMD_FORMAT input_fmd_formt, DPFJ_FMD_FORMAT output_fmd_format, MyFMD* input_fmd, MyFMDSize input_fmd_size) {
            
            MyFMD output_fmd[MY_MAX_FMD_SIZE]; // will hold the output fmd
            MyFMDSize output_fmd_size; // will hold the original size of the output fmd

            int conversion_result = dpfj_fmd_convert(input_fmd_formt, input_fmd, input_fmd_size, output_fmd_format, output_fmd, &output_fmd_size);

            map<MyFMD*, MyFMDSize*> converted_fmd_result;

            if (conversion_result == DPFJ_SUCCESS) {
                cout << "FMD coverted successfully..." << endl;
                converted_fmd_result[output_fmd] = &output_fmd_size;

                return converted_fmd_result;
            }

            cout << "Unknown error..." << endl;
            return converted_fmd_result;

        }
};


void RunServer() {
    const char * port = getenv("PORT");
    string  server_address("0.0.0.0:");
    if (port && strlen(port) <= strlen(MAX_PORT)) {
        string user_port(port);
        server_address.append(port);
    }
    else {
        server_address.append(DEFAULT_PORT);
    }

    FingerPrintImpl service;

    ServerBuilder builder;
    builder.AddListeningPort(server_address, grpc::InsecureServerCredentials());
    builder.RegisterService(&service);

    unique_ptr<Server> server(builder.BuildAndStart());
    cout << "Server started, listening on " << server_address << endl;
    server->Wait();
}   

int main() {
    RunServer();
}