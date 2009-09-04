<?php

	Class extension_URL_Schema_Manipulator extends Extension{

		public function about(){
			return array('name' => 'URL Schema Manipulator',
						 'version' => '1.0',
						 'release-date' => '2009-09-04',
						 'author' => array('name' => 'Alistair Kearney',
										   'website' => 'http://symphony-cms.com',
										   'email' => 'alistair@symphony-cms.com')
				 		);
		}
		
		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/frontend/',
					'delegate' => 'FrontendPageResolved',
					'callback' => 'cbManipulateURLSchema'
				),
			);
		}

		public function cbManipulateURLSchema($context=NULL){
			if(isset($context['env']['url']['category']) && strtolower($context['env']['url']['category']) == 'all'){
				$context['env']['url']['category'] = NULL;
			}
			
			return;
		}

	}