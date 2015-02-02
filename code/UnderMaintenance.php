<?php
/**
 * Decorator to essentially create a static under construction {@link ErrorPage} in the assets folder 
 * with the aid of requireDefaultRecords().
 * 
 * @author Frank Mullenger <frankmullenger@gmail.com>
 * @package undermaintenance
 */
class UnderMaintenance_Decorator extends DataExtension {
   
  /**
   * Create an {@link ErrorPage} for status code 503
   * 
   * @see UnderMaintenance_Extension::onBeforeInit()
   * @see DataObjectDecorator::requireDefaultRecords()
   * @return Void
   */
  function requireDefaultRecords() {
    
    // Ensure that an assets path exists before we do any error page creation
		if(!file_exists(ASSETS_PATH)) {
			mkdir(ASSETS_PATH);
		}

		$pageUnderMaintenanceErrorPage = DataObject::get_one('ErrorPage', "\"ErrorCode\" = '503'");
		$pageUnderMaintenanceErrorPageExists = ($pageUnderMaintenanceErrorPage && $pageUnderMaintenanceErrorPage->exists()) ? true : false;
		$pageUnderMaintenanceErrorPagePath = ErrorPage::get_filepath_for_errorcode(503);
		if(!($pageUnderMaintenanceErrorPageExists && file_exists($pageUnderMaintenanceErrorPagePath))) {
			if(!$pageUnderMaintenanceErrorPageExists) {
				$pageUnderMaintenanceErrorPage = new ErrorPage();
				$pageUnderMaintenanceErrorPage->ErrorCode = 503;
				$pageUnderMaintenanceErrorPage->Title = _t('UnderMaintenance.TITLE', 'Under Maintenance');
				$pageUnderMaintenanceErrorPage->Content = _t('UnderMaintenance.CONTENT', '<h1>Maintenance Mode</h1>
				                <p>Sorry, this site is currently undergoing scheduled maintenance, please check back shortly.</p>');
				$pageUnderMaintenanceErrorPage->Status = 'New page';
				$pageUnderMaintenanceErrorPage->write();
				$pageUnderMaintenanceErrorPage->publish('Stage', 'Live');
    		}

			// Ensure a static error page is created from latest error page content
			$response = Director::test(Director::makeRelative($pageUnderMaintenanceErrorPage->Link()));
			if($fh = fopen($pageUnderMaintenanceErrorPagePath, 'w')) {
				$written = fwrite($fh, $response->getBody());
				fclose($fh);
			}

			if($written) {
				DB::alteration_message('503 error page created', 'created');
			} else {
				DB::alteration_message(sprintf('503 error page could not be created at %s. Please check permissions', $pageUnderMaintenanceErrorPagePath), 'error');
			}
		}
	}
}

/**
 * Extension to display an {@link ErrorPage} if necessary.
 * 
 * @author Frank Mullenger <frankmullenger@gmail.com>
 * @package undermaintenance
 */
class UnderMaintenance_Extension extends Extension {
   
  /**
   * If current logged in member is not an admin and not trying to log in to the admin
   * or run a /dev/build then display an {@link ErrorPage}.
   * 
   * @see UnderMaintenance_Decorator::requireDefaultRecords()
   * @return Void
   */
  public function onBeforeInit() {

    $siteConfig = SiteConfig::current_site_config();
    $siteUnderMaintenance = $siteConfig->UnderMaintenance;

    if ($siteUnderMaintenance) {
      
      //Check to see if running /dev/build
      $runningDevBuild = $this->owner && $this->owner->data() instanceof ErrorPage;
      
      if (!Permission::check('ADMIN') 
          && strpos($_SERVER['REQUEST_URI'], '/admin') === false 
          && strpos($_SERVER['REQUEST_URI'], '/Security') === false 
          //&& !Director::isDev()
          && !$runningDevBuild) {
        Debug::friendlyError(503);
        exit;
      }
    }
  }
}

/**
 * Decorator to add settings to config to make it easier to make the site live and 
 * turn off any 'Under Construction' pages.
 * 
 * @author Frank Mullenger <frankmullenger@gmail.com>
 * @package undermaintenance
 */
class UnderMaintenance_Settings extends DataExtension {
  
	// Add database field for flag to either display or hide under construction pages.
	static $db = array(
    'UnderMaintenance' => 'Boolean'
	);

	/**
	 * Adding field to allow CMS users to turn off under construction pages.
	 * 
	 * @see DataExtension::updateCMSFields()
	 */
  function updateCMSFields(FieldList $fields) {
    $fields->addFieldToTab('Root.Access', new HeaderField(
    	'UnderMaintenanceHeading', 
      _t('UnderMaintenance.SETTINGSHEADING', 'Is this site under construction?'), 
      2
    ));
    $fields->addFieldToTab('Root.Access', new CheckboxField(
    	'UnderMaintenance', 
    	_t('UnderMaintenance.SETTINGSCHECKBOXLABEL', '&nbsp; Display an under construction page?')
    ));
	}
}