<?php

chdir(__DIR__);
require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();


$row = 1;
$users = [];
if (($handle = fopen("users.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		if($row == 1) {
			$row++;
			continue;
		}
		$meta = [
			"club"  => $data[0],
			"name"  => $data[1] . " " . $data[2],
			"email" => $data[3]
		];

		try {
			$user = \App\User::where("email", $meta['email'])->firstOrFail();
		} catch(Exception $e) {
			$user = new \App\User();
			$user->name  = $meta['name'];
			$user->email = $meta['email'];
			$user->save();
		}

		try {
			$club = \App\Club::where("name", $meta['club'])->firstOrFail();
		} catch(Exception $e) {}

		try {
			$user_club = \App\UserClub::where("user_id", $user->id)->where("club_id", $club->id)->firstOrFail();
		} catch(Exception $e) {
			$user_club = new \App\UserClub();
			$user_club->user_id  = $user->id;
			$user_club->club_id  = $club->id;
			$user_club->save();
		}

	}
	fclose($handle);
}



/*
foreach(\App\Domain::all() as $domain) {
	print_r($domain->toArray());
}
*/

//print_r($app);

?>