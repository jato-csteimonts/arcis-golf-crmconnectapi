<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

$Options = getopt("d::s::", []);

// ../../iauditor-exporter/stein-exports/iauditor/*

use App\iAuditor\Audit;
use App\iAuditor\Category;
use App\iAuditor\Section;
use App\iAuditor\Item;
use App\iAuditor\Template;

foreach (glob("../../iauditor-exporter/stein-exports/iauditor/*.csv") as $filename) {
	echo "Importing '" . basename($filename) . "'...\n";



	$row = 1;
	$Headers = [];
	$Records = [];
	if (($handle = fopen($filename, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
			if($row === 1) {
				$Headers = $data;
			} else {
				$CurrRecord = [];
				foreach($data AS $index => $value) {
					$CurrRecord[$Headers[$index]] = trim($value);
				}

				////////////////////////////////////
				// First let's check the Template
				//
				try {
					$Template = Template::where("template_id", $CurrRecord['TemplateID'])->firstOrFail();
					$Template->update([
						"template_name" => $CurrRecord['TemplateName'],
						"template_author_id" => $CurrRecord['TemplateAuthorID'],
						"template_author_name" => $CurrRecord['TemplateAuthor'],
					]);
				} catch (Exception $e) {
					$Template = new Template();
					$Template->template_id = $CurrRecord['TemplateID'];
					$Template->template_name = $CurrRecord['TemplateName'];
					$Template->template_author_id = $CurrRecord['TemplateAuthorID'];
					$Template->template_author_name = $CurrRecord['TemplateAuthor'];
					$Template->save();
				}

				////////////////////////////////////////////////////////
				// Now let's import of update the actual Audit record
				//
				try {
					$Audit = Audit::where("audit_id", $CurrRecord['AuditID'])->firstOrFail();
					$Audit->update([
						"audit_author_id" => $CurrRecord['AuditAuthorID'],
						"audit_author_name" => $CurrRecord['AuditAuthor'],
						"audit_owner_id" => $CurrRecord['AuditOwnerID'],
						"audit_owner_name" => $CurrRecord['AuditOwner'],
						"audit_name" => $CurrRecord['AuditName'],
						"audit_site" => $CurrRecord['AuditSite'],
						"audit_area" => $CurrRecord['AuditArea'],
						"audit_region" => $CurrRecord['AuditRegion'],
						"audit_score" => $CurrRecord['AuditScore'] ?? null,
						"audit_max_score" => $CurrRecord['AuditMaxScore'] ?? null,
						"audit_score_percentage" => $CurrRecord['AuditScorePercentage'] ?? null,
						"audit_start_date" => $CurrRecord['DateStarted'] ? strftime("%Y-%m-%dT%H:%M:%S", strtotime($CurrRecord['DateStarted'])) : null,
						"audit_modified_date" => $CurrRecord['DateModified'] ? strftime("%Y-%m-%dT%H:%M:%S", strtotime($CurrRecord['DateModified'])) : null,
						"audit_finished_date" => $CurrRecord['DateCompleted'] ? strftime("%Y-%m-%dT%H:%M:%S", strtotime($CurrRecord['DateCompleted'])) : null,
						"audit_time_duration" => $CurrRecord['AuditScore'],
					]);
				} catch (Exception $e) {
					$Audit = new Audit();
					$Audit->audit_id = $CurrRecord['AuditID'];
					$Audit->audit_author_id = $CurrRecord['AuditAuthorID'];
					$Audit->audit_author_name = $CurrRecord['AuditAuthor'];
					$Audit->audit_owner_id = $CurrRecord['AuditOwnerID'];
					$Audit->audit_owner_name = $CurrRecord['AuditOwner'];
					$Audit->audit_name = $CurrRecord['AuditName'];
					$Audit->audit_site = $CurrRecord['AuditSite'];
					$Audit->audit_area = $CurrRecord['AuditArea'];
					$Audit->audit_region = $CurrRecord['AuditRegion'];
					$Audit->audit_score = $CurrRecord['AuditScore'] ?? null;
					$Audit->audit_max_score = $CurrRecord['AuditMaxScore'] ?? null;
					$Audit->audit_score_percentage = $CurrRecord['AuditScorePercentage'] ?? null;
					$Audit->audit_start_date = $CurrRecord['DateStarted'] ? strftime("%Y-%m-%dT%H:%M:%S", strtotime($CurrRecord['DateStarted'])) : null;
					$Audit->audit_modified_date = $CurrRecord['DateModified'] ? strftime("%Y-%m-%dT%H:%M:%S", strtotime($CurrRecord['DateModified'])) : null;
					$Audit->audit_finished_date = $CurrRecord['DateCompleted'] ? strftime("%Y-%m-%dT%H:%M:%S", strtotime($CurrRecord['DateCompleted'])) : null;
					$Audit->audit_time_duration = $CurrRecord['AuditScore'];
					$Audit->save();
				}

				//////////////////////////
				// Now let's do Sections
				//
				if($CurrRecord['ItemType'] == "section" && $CurrRecord['Label']) {
					try {
						$Section = Section::where("audit_section_name", $CurrRecord['Label'])->firstOrFail();
					} catch (Exception $e) {
						$Section = new Section();
						$Section->audit_section_name = $CurrRecord['Label'];
						$Section->save();
					}
				}

				////////////////////////////
				// Now let's do Categories
				//
				if($CurrRecord['ItemType'] == "category" && $CurrRecord['Label']) {
					try {
						$Category = Category::where("audit_category_name", $CurrRecord['Label'])->firstOrFail();
					} catch (Exception $e) {
						$Category = new Category();
						$Category->audit_category_name = $CurrRecord['Label'];
						$Category->save();
					}
				}

				////////////////////////////////////////////////////////////////////////////
				// Now let's do Items, which are essentially Answers or Notes in the Audit
				//
				if($CurrRecord['ItemType'] == 'question' ||
				   strstr($CurrRecord['ItemType'], "information")) {

					$Label = $CurrRecord['Label'];
					if(strstr($CurrRecord['ItemType'], "information")) {
						$Label = 'Note';
					}

					$Response = $CurrRecord['Response'];
					if(strstr($CurrRecord['ItemType'], "information")) {
						$Response = $CurrRecord['Label'];
					}

					$CurrRecord['ItemScore']           = $CurrRecord['ItemScore'] == '' ? 0 : $CurrRecord['ItemScore'];
					$CurrRecord['ItemMaxScore']        = $CurrRecord['ItemMaxScore'] == '' ? 0 : $CurrRecord['ItemMaxScore'];
					$CurrRecord['ItemScorePercentage'] = $CurrRecord['ItemScorePercentage'] == '' ? 0.00 : $CurrRecord['ItemScorePercentage'];

					try {
						$Item = Item::where("audit_id", $Audit->id)->where("audit_item_id", $CurrRecord['ItemID'])->firstOrFail();
						$Item->update([
							'audit_id' => $Audit->id,
							'audit_section_id' => $Section ? $Section->id : null,
							'audit_category_id' => $Category ? $Category->id : null,
							'audit_item_sorting_index' => $CurrRecord['SortingIndex'],
							'audit_item_type' => $CurrRecord['ItemType'],
							'audit_item_label' => $Label,
							'audit_item_response' => $Response,
							'audit_item_score' => $CurrRecord['ItemScore'],
							'audit_item_max_score' => $CurrRecord['ItemMaxScore'],
							'audit_item_score_percentage' => $CurrRecord['ItemScorePercentage'],
						]);
					} catch (Exception $e) {
						$Item = new Item();
						$Item->audit_id = $Audit->id;
						$Item->audit_item_id = $CurrRecord['ItemID'];
						$Item->audit_section_id = $Section ? $Section->id : null;
						$Item->audit_category_id = $Category ? $Category->id : null;
						$Item->audit_item_sorting_index = $CurrRecord['SortingIndex'];
						$Item->audit_item_type = $CurrRecord['ItemType'];
						$Item->audit_item_label = $Label;
						$Item->audit_item_response = $Response;
						$Item->audit_item_score = $CurrRecord['ItemScore'];
						$Item->audit_item_max_score = $CurrRecord['ItemMaxScore'];
						$Item->audit_item_score_percentage = $CurrRecord['ItemScorePercentage'];
						$Item->save();
					}
				}

			}
			$row++;
		}
		fclose($handle);
	}



}


?>