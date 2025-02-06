<?php

interface GitStatusCodeInterface {
	public const STATUS_OK 				= 200;
	public const STATUS_CREATED 		= 201;
	public const STATUS_MULTI_STATUS	= 207;
	public const STATUS_NOT_MODIFIED 	= 304;
	public const STATUS_UNAUTHORIZED	= 401;
	public const STATUS_NOT_FOUND 		= 404;
	public const STATUS_CONFLICT 		= 409;
	public const STATUS_VALIDATION_FAILED	= 422;

	public const STATUS_INTERNAL_SERVER_ERROR	= 500;
}
