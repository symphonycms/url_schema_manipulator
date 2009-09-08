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
				
				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'appendPreferences'
				),
				
				array(
					'page' => '/system/preferences/',
					'delegate' => 'Save',
					'callback' => '__SavePreferences'
				),
			);
		}

		public function cbManipulateURLSchema($context=NULL){
			
			$params_to_check = array('category', 'tag', 'country', 'audience');
			
			foreach($params_to_check as $p){
				if(isset($context['env']['url'][$p]) && strtolower($context['env']['url'][$p]) == 'all'){
					$context['env']['url'][$p] = NULL;
				}
			}
			
			return;
		}

		public function __SavePreferences($context){

			if(!is_array($context['settings'])) $context['settings'] = array('maintenance_mode' => array('enabled' => 'no'));
			
			elseif(!isset($context['settings']['maintenance_mode'])){
				$context['settings']['maintenance_mode'] = array('enabled' => 'no');
			}
			
		}

		public function appendPreferences($context){
			
			Administration::instance()->Page->addStylesheetToHead(URL . '/extensions/url_schema_manipulator/assets/styles.css', 'screen', 90);
			
			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', 'URL Schema Manipulation'));			

			$p = new XMLElement('p', 'Define triggers and rules for runtime manipulation of the URL schema values.');
			$p->setAttribute('class', 'help');
			$fieldset->appendChild($p);
			
			
			$div = new XMLElement('div');
			$div->setAttribute('class', 'subsection');
			$div->appendChild(new XMLElement('h3', __('URL Schema Rules')));
			
			$ol = new XMLElement('ol');
			
			if(is_array($fields['dynamic_xml']['namespace']['name'])){
				
				$namespaces = $fields['dynamic_xml']['namespace']['name'];
				$uri = $fields['dynamic_xml']['namespace']['uri'];
				
				for($ii = 0; $ii < count($namespaces); $ii++){
					
					$li = new XMLElement('li');
					$li->appendChild(new XMLElement('h4', 'Namespace'));

					$group = new XMLElement('div');
					$group->setAttribute('class', 'group');

					$label = Widget::Label(__('Name'));
					$label->appendChild(Widget::Input('fields[dynamic_xml][namespace][name][]', General::sanitize($namespaces[$ii])));
					$group->appendChild($label);

					$label = Widget::Label(__('URI'));
					$label->appendChild(Widget::Input('fields[dynamic_xml][namespace][uri][]', General::sanitize($uri[$ii])));
					$group->appendChild($label);

					$li->appendChild($group);
					$ol->appendChild($li);					
				}
			}
			
			$li = new XMLElement('li');
			$li->setAttribute('class', 'template');
			$li->appendChild(new XMLElement('h4', __('Rule')));
			
			$group = new XMLElement('div');
			$group->setAttribute('class', 'group');
			
			$label = Widget::Label(NULL, NULL, 'inline-duplicator');
			$label->setValue('<input value="category" name="fields[url-schema-manipulator][param]" size="14" /> <select class="inline" name="fields[url-schema-manipulator][operator]"><option>is equal to</option><option>is not equal to</option><option>contains</option></select> <input value="all" name="fields[url-schema-manipulator][test]" size="14" /> replace it with <input value="NULL" name="fields[url-schema-manipulator][replacement]" size="14" />');
			$group->appendChild($label);
			
			/*
			$label = Widget::Label(__('Parameter Name'));
			$label->appendChild(Widget::Input('fields[dynamic_xml][namespace][name][]'));
			$group->appendChild($label);
			
			$label = Widget::Label(__('Replacement Value'));
			$label->appendChild(Widget::Input('fields[dynamic_xml][namespace][uri][]'));
			$group->appendChild($label);			
			
			$label = Widget::Label(__('Replacement Value'));
			$label->appendChild(Widget::Input('fields[dynamic_xml][namespace][uri][]'));
			$group->appendChild($label);*/
			
			$li->appendChild($group);
			$ol->appendChild($li);
			
			$div->appendChild($ol);
			$fieldset->appendChild($div);

			$p = new XMLElement('p', 'Use <code>{$param}</code> syntax to specify dynamic portions. Use <code>NULL</code> to signify an empty value.');
			$p->setAttribute('class', 'help');
			$fieldset->appendChild($p);
									
			$context['wrapper']->appendChild($fieldset);
						
		}

	}