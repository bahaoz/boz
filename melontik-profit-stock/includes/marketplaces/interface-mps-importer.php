<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface MPS_Importer_Interface {
	public function import( $file_path );
}
