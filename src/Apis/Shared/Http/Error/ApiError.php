<?php

namespace App\Apis\Shared\Http\Error;

use Symfony\Component\HttpFoundation\Response;

class ApiError
{
	// INVALID VALUES
	public const CODE_INVALID_VALUE = 'invalid_value';

	// MISSING FIELDS
	public const CODE_MISSING_PARAM = 'missing_param';

	// NOT FOUND FIELDS
	public const CODE_NOT_FOUND = 'not_found';
	public const CODE_USER_LOGIN_NOT_ALLOWED = 'user_login_not_allowed';
	public const CODE_CUSTOMER_NOT_FOUND = 'customer_not_found';

	// PERMISSIONS FIELDS
	public const CODE_CHART_FORBIDDEN = 'forbidden_chart';
	public const CODE_FORBIDDEN_ACTION = 'forbidden_route';
	public const CODE_FORBIDEN_EMPTY_REQUEST = 'forbiden_empty_request';
	public const CODE_CHANGE_PASSWORD_REQUIRED = 'change_password_required';

	// LOGIC ERRORS
	public const CODE_TWO_FACTOR_REQUIRED = 'two_factor_required';
	public const CODE_GROUP_TARGET_INVALID = 'group_target_invalid';
	public const CODE_EMPTY_GROUPS = 'empty_groups';

	// EXCEPTIONS
	public const CODE_DUPLICATE_EMAIL = 'email_already_exists';
	public const CODE_DUPLICATE_NAME = 'name_already_exists';
	public const CODE_DUPLICATE_CODE = 'code_already_exists';
	public const CODE_NOT_ALLOWED_TO_LOGIN = 'user_not_allowed_to_login';
	public const CODE_USER_NEEDS_CUSTOMERS = 'user_has_not_customers';
	public const CODE_RECURSIVE_DEPENDENCY = 'recursive_dependency';
	public const CODE_ENTITY_EXISTS = 'entity_already_exits';
	public const CODE_CUSTOMERS_DOES_NOT_MATCH = 'customers_does_not_match';
	public const CODE_SELF_ASSIGN_ERROR = 'self_assign_error';
	public const CODE_ROW_ALREADY_EXISTS = 'row_already_exists';
	public const CODE_INACTIVE_ENTITY = 'inactive_entity';
	public const CODE_ERROR_FILE_TOO_BIG = 'file_size_too_big';
	public const CODE_AUTHENTICATION_FAILED = 'bad_credentials';
	public const CODE_XTRF_SESSION_EXPIRED = 'xtrf_session_expired';
	public const CODE_TFA_FAILED = 'tfa_failed';
	public const CODE_TOKEN_EXPIRED = 'token_expired';
	public const CODE_TOKEN_INVALID = 'token_invalid';
	public const CODE_UNRECOGNIZED_PARAMETER = 'unrecognized_parameter';
	public const CODE_UNABLE_UPLOAD_FILE = 'unable_upload_file';
	public const CODE_MANAGE_POLICY_EMPTY = 'empty_manage_policy';
	public const CODE_EMPTY_LIST = 'empty_list';
	public const CODE_EMPTY_TEMPLATE_ID = 'empty_template_id';
	public const CODE_UNABLE_DOWNLOAD_FILE = 'unable_download_file';
	public const CODE_UNABLE_CREATE_FILE = 'unable_create_file';
	public const CODE_XTRF_COMMUNICATION_ERROR = 'xtrf_communication_error';
	public const CODE_NOT_ENOUGH_PERMISSIONS = 'not_enough_permissions';
	public const DESTROY_TYPE_INVALID = 'destroy_type_invalid';
	public const CODE_UPLOAD_FILE_ERROR = 'upload_file_error';
	public const CODE_FILE_LINK_ERROR = 'link_file_error';
	public const CODE_ERROR_UNZIP = 'unzip_file_error';
	public const CODE_ZIP_ERROR = 'zip_file_error';
	public const CODE_PAYMENT_ERROR = 'payment_error';
	public const CODE_EMAIL_SEND_ERROR = 'email_send_error';
	public const CODE_MACRO_RUN_ERROR = 'macro_run_error';
	public const CODE_MACRO_RUN_STILL_PENDING = 'macro_run_still_pending';
	public const CODE_OAUTH2_ERROR = 'oauth2_error';
	public const CODE_UNSUPPORTED = 'unsupported';
	public const CODE_INTERNAL_ERROR = 'internal_error';
	public const CODE_FORBIDDEN_EMPTY_REQUEST = 'forbidden_empty_request';

	/**
	 * @var array
	 */
	public static $descriptions = [
		self::CODE_INVALID_VALUE => 'The value is invalid.',
		self::CODE_NOT_FOUND => 'The value not found',
		self::CODE_ZIP_ERROR => 'Error creating zip.',
		self::CODE_TOKEN_EXPIRED => 'Token has been expired.',
		self::CODE_CHART_FORBIDDEN => 'The requested chart is forbidden.',
		self::CODE_FORBIDDEN_ACTION => 'The requested route is forbidden.',
		self::CODE_UNSUPPORTED => 'We dont support that function yet.',
		self::CODE_INTERNAL_ERROR => 'Opps something went wrong.',
		self::CODE_USER_LOGIN_NOT_ALLOWED => 'User is not allowed to login.',
		self::CODE_ROW_ALREADY_EXISTS => 'The entity already exists.',
		self::CODE_CUSTOMERS_DOES_NOT_MATCH => 'This users does not belong to same customer.',
		self::CODE_SELF_ASSIGN_ERROR => 'Self assign is not valid operation.',
		self::CODE_DUPLICATE_EMAIL => 'The email already exists.',
		self::CODE_DUPLICATE_NAME => 'The name already exists.',
		self::CODE_DUPLICATE_CODE => 'The code already exists.',
		self::CODE_NOT_ALLOWED_TO_LOGIN => 'User is not allowed to login in Client Portal.',
		self::CODE_USER_NEEDS_CUSTOMERS => 'User has not customers assigned for login.',
		self::CODE_RECURSIVE_DEPENDENCY => 'The parent can not be in child list',
		self::CODE_ENTITY_EXISTS => 'The entity already exists.',
		self::CODE_INACTIVE_ENTITY => 'The entity is inactive.',
		self::CODE_ERROR_FILE_TOO_BIG => 'File is too big.',
		self::CODE_AUTHENTICATION_FAILED => 'Invalid credentials.',
		self::CODE_XTRF_SESSION_EXPIRED => 'Xtrf Session Expired',
		self::CODE_TFA_FAILED => 'TFA authentication failed.',
		self::CODE_TWO_FACTOR_REQUIRED => 'Waiting for two factor authentication',
		self::CODE_GROUP_TARGET_INVALID => 'That group does not belong to that type of entity.',
		self::CODE_EMPTY_GROUPS => 'Entity does not have groups assigned.',
		self::CODE_TOKEN_INVALID => 'Invalid token.',
		self::CODE_FORBIDEN_EMPTY_REQUEST => 'Empty request is not allowed.',
		self::CODE_CHANGE_PASSWORD_REQUIRED => 'Change password required.',
		self::CODE_XTRF_COMMUNICATION_ERROR => 'Remote api communication error.',
		self::CODE_NOT_ENOUGH_PERMISSIONS => 'You have not enough permissions for that options. You can access to this action but some parameters are forbiden for your level.',
		self::CODE_UNABLE_UPLOAD_FILE => 'Unable to upload file from API.',
		self::CODE_MANAGE_POLICY_EMPTY => 'User has empty Manage Policy.',
		self::CODE_EMPTY_LIST => 'List can not be empty.',
		self::CODE_EMPTY_TEMPLATE_ID => 'Template id can not be empty.',
		self::CODE_UNABLE_DOWNLOAD_FILE => 'Unable to download file from API.',
		self::CODE_UNABLE_CREATE_FILE => 'Unable to create tmp file.',
		self::DESTROY_TYPE_INVALID => 'Only public users can be destroyed.',
		self::CODE_UPLOAD_FILE_ERROR => 'Upload file error.',
		self::CODE_FILE_LINK_ERROR => 'Error while generting file public link.',
		self::CODE_ERROR_UNZIP => 'Unzip file error.',
		self::CODE_PAYMENT_ERROR => 'Payment processor return error. Check logs for more details.',
		self::CODE_EMAIL_SEND_ERROR => 'Email could not be sent.',
		self::CODE_MACRO_RUN_ERROR => 'Error running macro in xtrf.',
		self::CODE_MACRO_RUN_STILL_PENDING => 'Macro run still pending. Delay needed.',
		self::CODE_UNRECOGNIZED_PARAMETER => 'Parameter not recognized.',
		self::CODE_FORBIDDEN_EMPTY_REQUEST => 'Empty request is not allowed.',
		self::CODE_MISSING_PARAM => 'The param is missing.',
		self::CODE_CUSTOMER_NOT_FOUND => 'Customer not found.',
		Response::HTTP_INTERNAL_SERVER_ERROR => 'Unexpected error.',
	];
}
