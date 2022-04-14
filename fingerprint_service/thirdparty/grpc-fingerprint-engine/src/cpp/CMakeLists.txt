# Copyright 2018 gRPC authors.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#
# cmake build file for C++ route_guide example.
# Assumes protobuf and gRPC have been installed using cmake.
# See cmake_externalproject/CMakeLists.txt for all-in-one cmake build
# that automatically builds all the dependencies before building route_guide.

cmake_minimum_required(VERSION 3.5.1)

project(FingerPrint C CXX)

# include(../cmake/common.cmake)
include("${CMAKE_SOURCE_DIR}/cmake/common.cmake")

# Proto file
# get_filename_component(fingerprint_proto "../../../protos/fingerprint.proto" ABSOLUTE)
get_filename_component(fingerprint_proto "${CMAKE_SOURCE_DIR}/../protos/fingerprint.proto" ABSOLUTE)
get_filename_component(fingerprint_proto_path "${fingerprint_proto}" PATH)

# Generated sources
set(fingerprint_proto_srcs "${CMAKE_CURRENT_BINARY_DIR}/fingerprint.pb.cc")
set(fingerprint_proto_hdrs "${CMAKE_CURRENT_BINARY_DIR}/fingerprint.pb.h")
set(fingerprint_grpc_srcs "${CMAKE_CURRENT_BINARY_DIR}/fingerprint.grpc.pb.cc")
set(fingerprint_grpc_hdrs "${CMAKE_CURRENT_BINARY_DIR}/fingerprint.grpc.pb.h")

# this custom command generate cpp files from the proto file
add_custom_command(
      OUTPUT "${fingerprint_proto_srcs}" "${fingerprint_proto_hdrs}" "${fingerprint_grpc_srcs}" "${fingerprint_grpc_hdrs}"
      COMMAND ${_PROTOBUF_PROTOC}
      ARGS --grpc_out "${CMAKE_CURRENT_BINARY_DIR}"
        --cpp_out "${CMAKE_CURRENT_BINARY_DIR}"
        -I "${fingerprint_proto_path}"
        --plugin=protoc-gen-grpc="${_GRPC_CPP_PLUGIN_EXECUTABLE}"
        "${fingerprint_proto}"
      DEPENDS "${fingerprint_proto}")

# Include generated *.pb.h files
include_directories("${CMAKE_CURRENT_BINARY_DIR}")
# Include *.h files
include_directories("${CMAKE_SOURCE_DIR}")

set(my_base_64_src "${CMAKE_SOURCE_DIR}/base64.h")
set(my_base64_hdr "${CMAKE_SOURCE_DIR}/base64.cc")

# fingerprint_grpc_proto
add_library(fingerprint_grpc_proto
  ${fingerprint_grpc_srcs}
  ${fingerprint_grpc_hdrs}
  ${fingerprint_proto_srcs}
  ${fingerprint_proto_hdrs})

target_link_libraries(fingerprint_grpc_proto
  ${_REFLECTION}
  ${_GRPC_GRPCPP}
  ${_PROTOBUF_LIBPROTOBUF})

# fingerprint engine include
add_library(dp_fingerprint_engine STATIC IMPORTED)
set_property(TARGET dp_fingerprint_engine PROPERTY IMPORTED_LOCATION "/lib/libdpfj.so")

# base64 include
add_library(my_base64 ${my_base_64_src} ${my_base64_hdr})

# Targets fingerprint_(client|server)
foreach(_target fingerprint_server)
  add_executable(${_target}
    "${_target}.cc")
  target_link_libraries(${_target}
    fingerprint_grpc_proto
    dp_fingerprint_engine
    my_base64
    ${_REFLECTION}
    ${_GRPC_GRPCPP}
    ${_PROTOBUF_LIBPROTOBUF})
endforeach()
