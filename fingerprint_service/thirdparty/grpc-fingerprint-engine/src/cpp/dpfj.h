/**
\file dpfj.h

\copyright (c) 2011 DigitalPersona, Inc

\brief U.are.U SDK FingerJet Engine API

Data types and functions for feature extraction, matching and conversion.

\version 2.0.0
*/

#ifndef __DPFJ_H__
#define __DPFJ_H__


/** \cond NEVER */
#ifndef DPAPICALL
#	if defined(_WIN32) || defined(_WIN64)
#		ifdef WINCE
#			define DPAPICALL __cdecl
#		else
#			define DPAPICALL __stdcall
#		endif
#	else
#		define DPAPICALL
#	endif
#endif

#ifndef NULL
#	ifdef __cplusplus
#		define NULL    0
#	else
#		define NULL    ((void *)0)
#	endif
#endif

#ifndef DPERROR
#	define _DP_FACILITY  0x05BA
#	define DPERROR(err)  ((int)err | (_DP_FACILITY << 16))
#endif /* DPERROR */

#define DPFJ_API_VERSION_MAJOR 1
#define DPFJ_API_VERSION_MINOR 0
/** \endcond */


/****************************************************************************************************
 Error codes
****************************************************************************************************/

/**
\brief API call succeeded.
*/
#define DPFJ_SUCCESS             0

/**
\brief API call is not implemented.
*/
#define DPFJ_E_NOT_IMPLEMENTED   DPERROR(0x0a)

/**
\brief Unspecified failure.

"Catch-all" generic failure code. Can be returned by all API calls in case of failure, when the reason for the failure is unknown or cannot be specified.
 */
#define DPFJ_E_FAILURE           DPERROR(0x0b)

/**
\brief No data is available.
*/
#define DPFJ_E_NO_DATA           DPERROR(0x0c)

/**
\brief Memory allocated by application is not enough to contain data which is expected.
*/
#define DPFJ_E_MORE_DATA         DPERROR(0x0d)

/**
\brief One or more parameters passed to the API call are invalid.
*/
#define DPFJ_E_INVALID_PARAMETER DPERROR(0x14)

/**
\brief FID is invalid.
*/
#define DPFJ_E_INVALID_FID     DPERROR(0x65)

/**
\brief Image is too small.
*/
#define DPFJ_E_TOO_SMALL_AREA    DPERROR(0x66)

/**
\brief FMD is invalid.
*/
#define DPFJ_E_INVALID_FMD       DPERROR(0xc9)

/**
\brief Enrollment operation is in progress.
*/
#define DPFJ_E_ENROLLMENT_IN_PROGRESS  DPERROR(0x12d)

/**
\brief Enrollment operation has not begun.
*/
#define DPFJ_E_ENROLLMENT_NOT_STARTED  DPERROR(0x12e)

/**
\brief Not enough in the pool of FMDs to create enrollment FMD.
*/
#define DPFJ_E_ENROLLMENT_NOT_READY  DPERROR(0x12f)

/**
\brief Unable to create enrollment FMD with the collected set of FMDs.
*/
#define DPFJ_E_ENROLLMENT_INVALID_SET  DPERROR(0x130)

/****************************************************************************************************
 Data types and definitions
****************************************************************************************************/

/**
\brief Normalized value when probability = 1.
*/
#define DPFJ_PROBABILITY_ONE  0x7fffffff

/** 
\brief Fingerprint Image Data (FID) Format.
*/
typedef int DPFJ_FID_FORMAT;

#define	DPFJ_FID_ANSI_381_2004     0x001B0401 /**< ANSI INSITS 381-2004 format */
#define DPFJ_FID_ISO_19794_4_2005  0x01010007 /**< ISO IEC 19794-4-2005 format */

/**
\brief Fingerptint Minutiae Data (FMD) Format.
*/
typedef int DPFJ_FMD_FORMAT;

#define DPFJ_FMD_ANSI_378_2004       0x001B0001 /**< ANSI INSITS 378-2004 Fingerprint Minutiae Data format */
#define DPFJ_FMD_ISO_19794_2_2005    0x01010001 /**< ISO IEC 19794-2-2005 Fingerprint Minutiae Data format */
#define DPFJ_FMD_DP_PRE_REG_FEATURES 0          /**< deprecated DigitalPersona pre-registration feature set format */
#define DPFJ_FMD_DP_REG_FEATURES     1          /**< deprecated DigitalPersona registration template format */
#define DPFJ_FMD_DP_VER_FEATURES     2          /**< deprecated DigitalPersona verification feature set format */

/** \brief Defines finger position.
	
Finger position according to ANSI 378-2004 and ISO 19794-2-2005 standards.
*/
typedef int DPFJ_FINGER_POSITION;

#define DPFJ_POSITION_UNKNOWN  0  /**< position unknown */
#define DPFJ_POSITION_RTHUMB   1  /**< right thumb */
#define DPFJ_POSITION_RINDEX   2  /**< right index finger */
#define DPFJ_POSITION_RMIDDLE  3  /**< right middle finger */
#define DPFJ_POSITION_RRING    4  /**< right ring finger */
#define DPFJ_POSITION_RLITTLE  5  /**< right little finger */
#define DPFJ_POSITION_LTHUMB   6  /**< left thumb */
#define DPFJ_POSITION_LINDEX   7  /**< left index finger */
#define DPFJ_POSITION_LMIDDLE  8  /**< left middle finger */
#define DPFJ_POSITION_LRING    9  /**< left ring finger */
#define DPFJ_POSITION_LLITTLE  10 /**< left little finger */

/** 
\brief Defines impression type.

Impression type according to ANSI 378-2004 and ISO 19794-2-2005 standards
*/
typedef int DPFJ_SCAN_TYPE;
	
#define DPFJ_SCAN_LIVE_PLAIN     0 
#define DPFJ_SCAN_LIVE_ROLLED    1
#define DPFJ_SCAN_NONLIVE_PLAIN  2
#define DPFJ_SCAN_NONLIVE_ROLLED 3
#define DPFJ_SCAN_SWIPE          8  

/**
\brief Defines matching engine to use.
*/
typedef int DPFJ_ENGINE_TYPE;

#define DPFJ_ENGINE_DPFJ                 0 /**< DigitalPersona FingerJet matching engine */
#define DPFJ_ENGINE_INNOVATRICS_ANSIISO  1 /**< Innovatrics ANSI ISO Generator and Matcher */

/** 
\brief Reader handle. 

Reader handle acquired by calling dpfpdd_open().
*/
typedef void* DPFJ_DEV;


/** \cond NEVER */
#define DPFJ_FID_ANSI_381_2004_RECORD_HEADER_LENGTH    36
#define DPFJ_FID_ISO_19794_4_2005_RECORD_HEADER_LENGTH 32
#define DPFJ_FID_ANSI_ISO_VIEW_HEADER_LENGTH           14

#define DPFJ_FMD_ANSI_378_2004_RECORD_HEADER_LENGTH    26
#define DPFJ_FMD_ISO_19794_2_2005_RECORD_HEADER_LENGTH 24
#define DPFJ_FMD_ANSI_ISO_VIEW_HEADER_LENGTH           4
#define DPFJ_FMD_ANSI_ISO_MINITIA_LENGTH               6
/** \endcond */

/**
\brief Maximum size of a single-view FMD with no extended data block.
*/
#define MAX_FMD_SIZE (DPFJ_FMD_ANSI_378_2004_RECORD_HEADER_LENGTH + DPFJ_FMD_ANSI_ISO_VIEW_HEADER_LENGTH + 255 * DPFJ_FMD_ANSI_ISO_MINITIA_LENGTH + 2)

/**
\brief API Version information. 
*/
typedef struct dpfj_ver_info {
	int major;       /**< major version number */
	int minor;       /**< minor version number */
	int maintenance; /**< maintenance or revision number */
} DPFJ_VER_INFO;

/**
\brief Complete information about library/SDK.
*/
typedef struct dpfj_version {
	unsigned int    size;  /**< Size of the structure, in bytes */	
	DPFJ_VER_INFO lib_ver; /**< file version of the library/SDK */
	DPFJ_VER_INFO api_ver; /**< version of the API */
} DPFJ_VERSION;

/**
\brief Candidate, result of identification.
*/
typedef struct dpfj_candidate{
 	unsigned int size;     /**< size of the structure, in bytes */
	unsigned int fmd_idx;  /**< index of the FMD in the input array */
	unsigned int view_idx; /**< index of the view in the FMD */
} DPFJ_CANDIDATE;


/****************************************************************************************************
 API calls
****************************************************************************************************/

#ifdef __cplusplus
extern "C" {
#endif /* __cplusplus */

	/**
	\brief Query the library and API version information. 
	
	\param ver  		[in] Pointer to the empty structure (per DPFJ_VERSION); [out] Pointer to structure containing version information
	\return DPFJ_SUCCESS:   Version information was acquired;
	\return DPFJ_E_FAILURE: Failed to acquire version information.
	*/
	int DPAPICALL dpfj_version( 
		DPFJ_VERSION* ver
	);

	/**
	\brief Select matching engine. 

	DigitalPersona FingerJet is default engine used if this function is not called. FingerJet is available on all platforms and does not require
	open reader (parameter hdev can be NULL). Not every other engine is available on every platform. Some engines require valid handle from 
	opened reader to be supplied.
	
	\param hdev 		[in] Reader handle.
	\param engine  		[in] Matching engine to use.
	\return DPFJ_SUCCESS:           Engine is selected;
	\return DPFJ_E_NOT_IMPLEMENTED: Requested engine is not supported of this platform.
	*/
	int DPAPICALL dpfj_select_engine(
		DPFJ_DEV hdev,
		DPFJ_ENGINE_TYPE engine
	);


	/**
	\brief Extracts features and creates an FMD from a raw image. 

	When you do a fingerprint capture, you can receive a raw image or a FID. If you specify a raw image, you can then extract features into an FMD using this function.
	The raw image is just a buffer of pixels. This function works with raw images that have
	 - 8 bits per pixel
	 - no padding
	 - square pixels (dpi is the same for horizontal and vertical)

	The size of the resulting FMD will vary depending on the minutiae in a specific fingerprint.  The maximum possible size of a single-view FMD is MAX_FMD_SIZE. 
	If the value pointed to by fmd_size is zero, the function will return with the error code DPFJ_E_MORE_DATA and the required size will be stored in the 
	value pointed to by fmd_size. In order to determine the size, this function processes the image, extracts features and discards the FMD, so it takes significant
	processing time.  However if memory shortages are a key issue, this allows you to allocate memory more efficiently at the expense of processing time.
	If memory is available, you will get the best performance if you always allocate MAX_FMD_SIZE for the FMD.
	The value pointed to by fmd_size will always be returned as the actual size of the FMD that was extracted.	

	\param image_data    pointer to the image data
	\param image_size    size of the image data
	\param image_width   width of the image
	\param image_height  height of the image
	\param image_dpi     resolution of the image
	\param finger_pos    position of the finger
	\param cbeff_id      CBEFF product ID, from IBIA registry
	\param fmd_type      type of the FMD
	\param fmd           pointer to recieve FMD
	\param fmd_size      pointer to allocated size for the FMD, pointer to receive the actual size of the FMD
	\return DPFJ_SUCCESS:             FMD was created;
	\return DPFJ_E_MORE_DATA:         Features extracted, but allocated memory is not sufficient for FMD. The required memory size is in the fmd_size.
	\return DPFJ_E_INVALID_PARAMETER: One or more parameters passed are invalid.
	\return DPFJ_E_FAILURE:           Failed to create FMD.
	*/
	int DPAPICALL dpfj_create_fmd_from_raw (
 		const unsigned char* image_data,
		unsigned int         image_size,
 		unsigned int         image_width,
 		unsigned int         image_height,
		unsigned int         image_dpi,
		DPFJ_FINGER_POSITION finger_pos,
		unsigned int         cbeff_id,
 		DPFJ_FMD_FORMAT      fmd_type,
 		unsigned char*       fmd,
 		unsigned int*        fmd_size 
	);

	/**
	\brief Extracts features and creates an FMD from an ANSI or ISO image. 
	
	This function works with FIDs that have
	 - 8 bits per pixel
	 - no padding
	 - square pixels (dpi is the same for horizontal and vertical)
	The size of the resulting FMD will vary depending on the minutiae in a specific fingerprint.  The maximum possible size of a single-view FMD is MAX_FMD_SIZE. 
	If the value pointed to by fmd_size is zero, the function will return with the error code DPFJ_E_MORE_DATA and the required size will be stored in the value 
	pointed to by fmd_size. In order to determine the size, this function processes the image, extracts the FMD and discards the FMD, so it takes significant processing time.  
	However if memory shortages are a key issue, this allows you to allocate memory more efficiently at the expense of processing time.
	If memory is available, you will get the best performance if you always allocate MAX_FMD_SIZE for the FMD.
	The value pointed to by fmd_size will always be returned as the actual size of the FMD that was extracted.

	\param fid_type  type of the FID
	\param fid       pointer to the FID data
	\param fid_size  size of the FID data
	\param fmd_type  type of the FMD
	\param fmd       pointer to recieve FMD data
	\param fmd_size  pointer to allocated size for the FMD, pointer to receive the actual size of the FMD
	\return DPFJ_SUCCESS:             FMD was created;
	\return DPFJ_E_MORE_DATA:         Features extracted, but allocated memory is not sufficient for FMD. The required memory size is in the fmd_size.
	\return DPFJ_E_INVALID_PARAMETER: One or more parameters passed are invalid.
	\return DPFJ_E_FAILURE:           Failed to create FMD.
	*/
	int DPAPICALL dpfj_create_fmd_from_fid (
		DPFJ_FID_FORMAT      fid_type,
 		const unsigned char* fid,
		unsigned int         fid_size,
 		DPFJ_FMD_FORMAT      fmd_type,
 		unsigned char*       fmd,
 		unsigned int*        fmd_size 
	);

	/**
	\brief Compares two fingerprints. 

	Given two single views from two FMDs, this function returns a <b>dissimilarity score</b> indicating the quality of the match.  
	The dissimilarity scores returned values are between:
		0=match
		maxint=no match
	Values close to 0 indicate very close matches, values closer to maxint indicate very poor matches.
	For a discussion of  how to evaluate dissimilarity scores, as well as the statistical validity of the dissimilarity score and error rates, consult the Developer Guide. 
	The dpfj_compare function returns DPFJ_SUCCESS if it is able to compare the fingerprints successfully (i.e., the FMDs are valid and correctly formed). 
	However that does not mean that the fingerprints matched. To check whether they matched, you must look at the dissimilarity score.

	\param fmd1_type        type of the first FMD
	\param fmd1             pointer to the first FMD
	\param fmd1_size        size of the first FMD
	\param fmd1_view_idx    index of the view
	\param fmd2_type        type of the second FMD
	\param fmd2             pointer to the second FMD
	\param fmd2_size size   of the second FMD
	\param fmd2_view_idx    index of the view
	\param score            pointer to receive dissimilarity score
	\return DPFJ_SUCCESS:   Comparison finished;
	\return DPFJ_E_FAILURE: Unknown error.
	*/
	int DPAPICALL dpfj_compare(
		DPFJ_FMD_FORMAT  fmd1_type, 
		unsigned char*   fmd1, 
		unsigned int     fmd1_size, 
		unsigned int     fmd1_view_idx,
		DPFJ_FMD_FORMAT  fmd2_type, 
		unsigned char*   fmd2, 
		unsigned int     fmd2_size, 
		unsigned int     fmd2_view_idx, 
		unsigned int*    score
	);

	/**
	\brief Compares a single fingerprint to an array of fingerprints.
	  
	This function takes as inputs:
		- a single view in an FMD
		- an array of FMDs (each FMD can contain up to 16 views) to compare
		- the desired number of candidates to return 
		- the threshold for False Positive Identification Rate that is permitted
	This function compares a single view against an array of FMDs.  Each time view has a score lower than the threshold, that view is marked as a possible candidate. 
	Then when all possible candidates are identified (i.e., they meet the threshold), they are ranked by their score.  Finally, the function returns as many 
	candidates as requested, based on the candidates with the lowest dissimilarity score.  
	For a discussion of setting the threshold as well as the statistical validity of the dissimilarity score and error rates, consult the Developer Guide.    
	
	\param fmd1_type         type of the FMDs
	\param fmd1              pointer to the first FMD data
	\param fmd1_size         size of the first FMD data
	\param fmd1_view_idx     index of the view
	\param fmds_type         type of the FMDs in the fmds array
	\param fmds_cnt          number of FMDs in the fmds array
	\param fmds              array of FMDs
	\param fmds_size         array of sizes of the FMDs data
	\param threshold_score   target threshold on degree of dissimilarity 
	\param candidate_cnt     [in] number of allocated entries in the candidates array; [out] receives the actual number of candidates filled in the array as a result of identification
	\param candidates        array of candidates
	\return DPFJ_SUCESS:    Identification finished;
	\return DPFJ_E_FAILURE: Unknown error.
	*/
	int DPAPICALL dpfj_identify(
 		DPFJ_FMD_FORMAT  fmd1_type,
		unsigned char*   fmd1,
		unsigned int     fmd1_size,
 		unsigned int     fmd1_view_idx,
		DPFJ_FMD_FORMAT  fmds_type,
 		unsigned int     fmds_cnt,
		unsigned char**  fmds,
		unsigned int*    fmds_size,
		unsigned int     threshold_score,
 		unsigned int*    candidate_cnt,
 		DPFJ_CANDIDATE*  candidates
	);

	/**
	\brief Starts enrollment operation.

	\param fmd_type type of FMD to produce as a result of enrollment operation
	\return DPFJ_SUCCESS:                  Enrollment started.
	\return DPFJ_E_INVALID_PARAMETER:      Requested FMD type is invalid.
	\return DPFJ_E_ENROLLMENT_IN_PROGRESS: Another enrollment operation is in prgress.
	\return DPFJ_E_FAILURE:                Unknown error.
	*/
	int DPAPICALL dpfj_start_enrollment(
		DPFJ_FMD_FORMAT fmd_type
	);

	/**
	\brief Adds FMD to enrollment operation.

	Add an FMD to the pool of FMDs for enrollment and return a flag indicating when the enrollment is ready.
	This function must be called before dpfj_create_enrollment_fmd.

	\param fmd_type       type of the FMD.
	\param fmd            pointer to the FMD data.
	\param fmd_size       size of the FMD data.
	\param fmd_view_idx   index of the view
	\return DPFJ_SUCCESS:                  FMD added, enrollment is ready.
	\return DPFJ_E_MORE_DATA:              FMD added, more FMDs for enrollment required.
	\return DPFJ_E_INVALID_PARAMETER:      One or more parameters passed are invalid.
	\return DPFJ_E_ENROLLMENT_NOT_STARTED: Enrollment is not started.
	\return DPFJ_E_FAILURE:                Unknown error.
	*/
	int DPAPICALL dpfj_add_to_enrollment(
		DPFJ_FMD_FORMAT  fmd_type,
		unsigned char*   fmd,
		unsigned int     fmd_size,
		unsigned int     fmd_view_idx
	);

	/**
	\brief Creates and returns enrollment FMD.

	Create an FMD for an enrolled finger. The output FMD is suitable for storing in a database of enrolled users.  
	Some applications like voting, banking and law enforcement require that you check for duplicate fingerprints before storing a new fingerprint in the database.
	For ANSI/ISO formats, the enrollment FMD is a standard FMD (the same as an FMD generated by the extraction function). For the DigitalPersona data format, 
	the enrollment FMD uses the "fingerprint template" format as used by legacy DigitalPersona applications.
	This function must be called after dpfj_add_to_enrollment.
	The size of the resulting FMD will vary depending on the minutiae in the fingerprint(s) that were enrolled.  The maximum possible size of an FMD is MAX_FMD_SIZE. 
	If the value pointed to by fmd_size is zero, the function will return with the error code DPFJ_E_MORE_DATA and the required size will be stored in the value pointed to by fmd_size. 
	In order to determine the size, this function processes the image, extracts features and discards the FMD, so it takes significant processing time.  
	However if memory shortages are a key issue, this allows you to allocate memory more efficiently at the expense of processing time.
	If memory is available, you will get the best performance if you always allocate MAX_FMD_SIZE for the FMD.
	The value pointed to by fmd_size will always be returned as the actual size of the FMD that was extracted.

	\param fmd            pointer to recieve FMD data
	\param fmd_size       pointer to allocated size for the FMD data, pointer to receive the actual size of the FMD data
	\return DPFJ_SUCCESS:                  FMD created.
	\return DPFJ_E_MORE_DATA:              FMD created, but allocated memory is not sufficient. The required memory size is in the fmd_size.
	\return DPFJ_E_INVALID_PARAMETER:      One or more parameters passed are invalid.
	\return DPFJ_E_ENROLLMENT_NOT_STARTED: Enrollment is not started.
	\return DPFJ_E_FAILURE:                Unknown error.
	*/
	int DPAPICALL dpfj_create_enrollment_fmd(
		unsigned char*  fmd,
		unsigned int*   fmd_size
	);

	/**
	\brief Ends enrollment operation, releases memory.

	This function releases resources used during the enrollment process.  Call after enrollment is complete.
	\return DPFJ_SUCCESS:   Enrollment ended.
	\return DPFJ_E_FAILURE: Unknown error.
	*/
	int DPAPICALL dpfj_finish_enrollment();

	/**
	\brief Converts an FMD from any supported format to any other supported format.
	
	\param fmd1_type  type of the input FMD
	\param fmd1       pointer to the input FMD data
	\param fmd1_size  size of the input FMD data
	\param fmd2_type  type of the target FMD
	\param fmd2       pointer to receive target FMD data
	\param fmd2_size  pointer to allocated size for the FMD data, pointer to receive the actual size of the FMD data
	\return DPFJ_SUCCESS:             FMD was converted;
	\return DPFJ_E_INVALID_PARAMETER: One or more parameters passed are invalid.
	\return DPFJ_E_FAILURE:           Failed to convert FMD.
	*/
	int DPAPICALL dpfj_fmd_convert(
		DPFJ_FMD_FORMAT  fmd1_type,
		unsigned char*   fmd1,
		unsigned int     fmd1_size,
		DPFJ_FMD_FORMAT  fmd2_type,
		unsigned char*   fmd2,
		unsigned int*    fmd2_size
	);

	/****************************************************************************************************
	 legacy DigitalPersona image format conversion
	****************************************************************************************************/

	/**
	\brief Converts legacy DigitalPersona image to the image in ANSI or ISO format. 
	
	\param dp_image         pointer to the DP image data
	\param dp_image_size    size of the DP image data
	\param fid_type         type of the FID
	\param fid_dpi          resolution of the FID, valid values are 500 and 1000
	\param rotate180        flag: rotate image, 0 - do not rotate, 1 - rotate
	\param fid              pointer to receive FID data
	\param fid_size         pointer to receive the size of the FID
	\return DPFJ_SUCCESS:   FID was created
	\return DPFJ_E_FAILURE: Failed to create FID
	*/
	int DPAPICALL dpfj_dp_fid_convert(
		const unsigned char* dp_image,
		unsigned int         dp_image_size,
		DPFJ_FID_FORMAT      fid_type,
		unsigned int         fid_dpi,
		unsigned int         rotate180,
		unsigned char*       fid,
		unsigned int*        fid_size
	);

	/****************************************************************************************************
	 raw image format conversion
	****************************************************************************************************/

	/**
	\brief Converts raw image to the image in ANSI or ISO format. 
	
	\param image_data       pointer to the image data
	\param image_size       size of the image data
	\param image_width      width of the image
	\param image_height     height of the image
	\param image_dpi        resolution of the image
	\param finger_pos       position of the finger
	\param cbeff_id         CBEFF product ID, from IBIA registry
	\param fid_type         type of the FID
	\param fid_dpi          resolution of the FID, valid values are 500 and 1000
	\param rotate180        flag: rotate image, 0 - do not rotate, 1 - rotate
	\param fid              pointer to receive FID data
	\param fid_size         pointer to receive the size of the FID
	\return DPFJ_SUCCESS:   FID was created
	\return DPFJ_E_FAILURE: Failed to create FID
	*/
	int DPAPICALL dpfj_raw_convert(
 		const unsigned char* image_data,
		unsigned int         image_size,
 		unsigned int         image_width,
 		unsigned int         image_height,
		unsigned int         image_dpi,
		DPFJ_FINGER_POSITION finger_pos,
		unsigned int         cbeff_id,
		DPFJ_FID_FORMAT      fid_type,
		unsigned int         fid_dpi,
		unsigned int         rotate180,
		unsigned char*       fid,
		unsigned int*        fid_size
	);

	/****************************************************************************************************
	 FID and FMD manipulation: types and definitions
	****************************************************************************************************/

	/** 
	\brief Define image properties.
	
	Structure defines image properties for FIDs in ANSI 381-2004 
	and ISO 19794-4-2005 formats.
	*/
	typedef struct dpfj_fid_record_params{
		unsigned int record_length;        /**< total length of the image, including headers and all views */
		unsigned int cbeff_id;             /**< CBEFF product identifier */
		unsigned int capture_device_id;    /**< vendor specified */
		unsigned int acquisition_level;    /**< from Table 1 in "ANSI INSITS 381-2004" */
		unsigned int finger_cnt;           /**< total number of fingerprints in the record, must be greater or equal to 1 */
		unsigned int scale_units;          /**< pixels/cm (2) or pixels/inch (1) */
		unsigned int scan_res;             /**< scan resolution */
		unsigned int image_res;            /**< image resolution */
		unsigned int bpp;                  /**< pixel depth, 1 - 16 bits */
		unsigned int compression;          /**< from Table 3 in "ANSI INSITS 381-2004" */
	} DPFJ_FID_RECORD_PARAMS;

	/**
	\brief Define fingerprint image view (FIV) properties.
	
	Structure defines image view properties for FIVs in ANSI 381-2004 
	and ISO 19794-4-2005 formats.
	*/
	typedef struct dpfj_fid_view_params{
		unsigned int         data_length;     /**< total length of the finger data including header */
		DPFJ_FINGER_POSITION finger_position; /**< finger position  */
		unsigned int         view_cnt;        /**< number of views associated with this finger, must be greater or equal to 1 */
		unsigned int         view_number;     /**< 1 - 256 */
		unsigned int         quality;         /**< 1 - 100 for ISO; 1 - 100, 254 for ANSI */
		DPFJ_SCAN_TYPE       impression_type; /**< impression type */
		unsigned int         width;           /**< width of the fingerprint view, in pixels */
		unsigned int         height;          /**< height of the fingerprint view, in pixels */
		unsigned char*       view_data;       /**< pointer to the view data */
	} DPFJ_FID_VIEW_PARAMS;

	/**
	\brief Define FMD properties.
	
	Structure defines minutiae data properties for FMDs in ANSI 378-2004 
	and ISO 19794-2-2005 formats.
	*/
	typedef struct dpfj_fmd_record_params{
		unsigned int record_length;           /**< total length of the image, including headers and all views */
		unsigned int cbeff_id;                /**< CBEFF product identifier */
		unsigned int capture_equipment_comp;  /**< 4 bits: compliance;  */
		unsigned int capture_equipment_id;    /**< 12 bits: capture device id, vendor specified */
		unsigned int width;                   /**< width of the fingerprint image, in pixels */
		unsigned int height;                  /**< height of the fingerprint image, in pixels */
		unsigned int resolution;              /**< resolution of the fingerprint image */
		unsigned int view_cnt;                /**< number of views */
	} DPFJ_FMD_RECORD_PARAMS;

	/**
	\brief Define fingerprint minutiae view (FMV) properties.
	
	Structure defines minutiae view properties for FMVs in ANSI 378-2004 
	and ISO 19794-2-2005 formats.
	*/
	typedef struct dpfj_fmd_view_params{
		DPFJ_FINGER_POSITION finger_position;  /**< 0 - 10, from Table 5 in "ANSI INSITS 381-2004" */
		unsigned int         view_number;      /**< 0 - 15 */
		DPFJ_SCAN_TYPE       impression_type;  /**< Table 2 in "ANSI INSITS 378-2004" */
		unsigned int         quality;          /**< 1 - 100  */
		unsigned int         minutia_cnt;      /**< number of minutiae */
		unsigned int         ext_block_length; /**< length of the extended data block, in bytes */
		unsigned char*       ext_block;        /**< pointer to the extended data block */
	} DPFJ_FMD_VIEW_PARAMS;

	/****************************************************************************************************
	 FID manipulation
	****************************************************************************************************/

	/**
	\brief Read image properties from FID.
	
	\param image_type  type of the FID (per DPFJ_FID_FORMAT)
	\param image       pointer to the FID
	\param params      pointer to the structure to receive image properties
	\return void
	*/
	void DPAPICALL dpfj_get_fid_record_params(
		DPFJ_FID_FORMAT         image_type,
		const unsigned char*    image,
		DPFJ_FID_RECORD_PARAMS* params
	);

	/** 
	\brief Writes image properties to FID.
	
	\param params      pointer to the structure containing image properties
	\param image_type  format of the FID (per DPFJ_FID_FORMAT)
	\param image       pointer to the FID
	\return void
	*/
	void DPAPICALL dpfj_set_fid_record_params(
		const DPFJ_FID_RECORD_PARAMS* params,
		DPFJ_FID_FORMAT               image_type,
		unsigned char*                image
	);

	/**
	\brief Returns pointer to the specified view from FID.
	
	\param image_type  type of the FID (per DPFJ_FID_FORMAT)
	\param image       pointer to the FID
	\param view_idx    view index
	\return offset to the specified view
	*/
	unsigned int DPAPICALL dpfj_get_fid_view_offset(
		DPFJ_FID_FORMAT      image_type,
		const unsigned char* image,
		unsigned int         view_idx
	);

	/**
	\brief Read image view properties from FID.
	
	\param view    pointer to the view from the FID
	\param params  pointer to the structure to receive view properties
	\return void
	*/
	void DPAPICALL dpfj_get_fid_view_params(
		const unsigned char*  view,
		DPFJ_FID_VIEW_PARAMS* params
	);

	/**
	\brief Write image view properties to FID.
	
	\param params  pointer to the structure containing view properties
	\param view    pointer to the view from the FID
	\return void
	*/
	void DPAPICALL dpfj_set_fid_view_params(
		const DPFJ_FID_VIEW_PARAMS* params,
		unsigned char*              view
	);

	/****************************************************************************************************
	 FMD manipulation
	****************************************************************************************************/

	/**
	\brief Read minutiae record properties from FMD.
	
	\param fmd_type   format of the FMD (per DPFJ_FMD_FORMAT)
	\param fmd        pointer to the FMD
	\param params     pointer to the structure to receive FMD properties
	\return void
	*/
	void DPAPICALL dpfj_get_fmd_record_params(
		DPFJ_FMD_FORMAT          fmd_type,
		const unsigned char*     fmd,
		DPFJ_FMD_RECORD_PARAMS*  params
	);

	/** \brief Write minutiae record properties to FMD.
	
	\param params     pointer to the structure containing FMD properties
	\param fmd_type   format of the FMD (per DPFJ_FMD_FORMAT)
	\param fmd        pointer to the FMD 
	\return void
	*/
	void DPAPICALL dpfj_set_fmd_record_params(
		const DPFJ_FMD_RECORD_PARAMS*  params,
		DPFJ_FMD_FORMAT                fmd_type,
		unsigned char*                fmd
	);

	/**
	\brief Return pointer to the specified view from FMD.
	
	\param fmd_type   format of the FMD (per DPFJ_FMD_FORMAT)
	\param fmd        pointer to the FMD
	\param view_idx   view index
	\return offset to the specified view
	*/
	unsigned int DPAPICALL dpfj_get_fmd_view_offset(
		DPFJ_FMD_FORMAT      fmd_type,
		const unsigned char* fmd,
		unsigned int         view_idx
	);

	/**
	\brief Read view properties from FMD.
	
	\param view    pointer to the view from the FMD
	\param params  pointer to the structure to receive view properties
	\return void
	*/
	void DPAPICALL dpfj_get_fmd_view_params(
		const unsigned char*   view,
		DPFJ_FMD_VIEW_PARAMS*  params
	);

	/**
	\brief Write view properties to FMD.
	
	\param params  pointer to the structure containing view properties
	\param view    pointer to the view from the FMD
	\return void
	*/
	void DPAPICALL dpfj_set_fmd_view_params(
		const DPFJ_FMD_VIEW_PARAMS* params,
		unsigned char*              view
	);


#ifdef __cplusplus
}
#endif /* __cplusplus */

#endif /* __DPFJ_H__ */
