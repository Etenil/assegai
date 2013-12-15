<?php

namespace assegai\exceptions
{
    /**
     * The file couldn't be uploaded.
     */
    class FileUploadError extends \Exception
    {
        function __construct($error_code) {
            parent::__construct($this->_translateErrorCode($error_code), $error_code);
        }

        protected function _translateErrorCode($error_code)
        {
            switch($error_code) {
            case UPLOAD_ERR_OK:
                return 'success';
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'the uploaded file is too large';
            case UPLOAD_ERR_PARTIAL:
                return 'the file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'the file was not uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'the server has no temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'failed to write upload to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'illegal file extension';
            }
        }
    }
}