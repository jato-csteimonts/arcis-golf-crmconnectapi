<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

exit;

$subdomains = [
	"events.desertpinesgolfclub.com",
	"events.eaglebrookclub.com",
	"events.forestparkgc.com",
	"events.foxmeadowcc.com",
	"events.golfclubatcincoranch.com",
	"events.highlandsgolfandtennis.com",
	"events.huntvalleygc.com",
	"events.ironhorsetx.com",
	"events.lasvegasgc.com",
	"events.losroblesgreens.com",
	"events.majesticoaksgc.com",
	"events.meadowlarkgc.com",
	"events.montgomerycc.com",
	"events.painteddesertgc.com",
	"events.plantationgolf.net",
	"events.ravenphx.com",
	"events.rubyhill.com",
	"events.ruffledfeathersgc.com",
	"events.shandinhillsgolf.com",
	"events.signatureofsoloncc.com",
	"events.stonecreekgc.com",
	"events.superstitionspringsgc.com",
	"events.tamarackgc.com",
	"events.tartanfields.com",
	"events.tatumranchgc.com",
	"events.theclubatpradera.com",
	"events.thepinerycc.com",
	"events.twincreeksgolf.com",
	"events.valenciagolfclub.com",
	"events.weymouthcc.com",
	"events.whitetailridgegolfclub.com",
	"holiday.eventsatlacentre.com",
	"join.ancalacc.com",
	"join.arrowheadccaz.com",
	"join.broadbaycc.com",
	"join.eaglebrookclub.com",
	"join.foxmeadowcc.com",
	"join.huntvalleygc.com",
	"join.montgomerycc.com",
	"join.oaksclubvalencia.com",
	"join.rubyhill.com",
	"join.signatureofsoloncc.com",
	"join.tartanfields.com",
	"join.tatumranchgc.com",
	"join.theclubatpradera.com",
	"join.theclubatsnoqualmieridge.com",
	"join.thepinerycc.com",
	"join.valenciagolfclub.com",
	"join.weymouthcc.com",
	"register.phoenixgolfevents.com",
	"wedding.eventsatlacentre.com"
];

foreach($subdomains as $subdomain) {

	$domain_data = explode(".", $subdomain);
	$domain = $domain_data[1] . "." . $domain_data[2];

	try {
		$domain_object = \App\Domain::where("domain", $subdomain)->firstOrFail();
		print("ERROR: Subdomain already exists: {$subdomain}\n");
		continue;
	} catch (\Exception $e) {}
	try {
		$domain_object = \App\Domain::where("domain", $domain)->firstOrFail();
	} catch (\Exception $e) {
		print("ERROR: Could not find domain: {$domain} ({$subdomain})\n");
		continue;
	}

	print("Importing Subdomain: {$subdomain} (Club ID: {$domain_object->club_id})\n");

	$new_domain_object = new \App\Domain();
	$new_domain_object->club_id = $domain_object->club_id;
	$new_domain_object->domain  = $subdomain;
	$new_domain_object->save();
}



/*
foreach(\App\Domain::all() as $domain) {
	print_r($domain->toArray());
}
*/

//print_r($app);

?>