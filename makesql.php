<?php
	require __DIR__ . '/vendor/autoload.php';
	require __DIR__ . '/variables.php';

	$faker = Faker\Factory::create();

	// if the file already exists just delete it.
	$file = "sql.sql";
	if(file_exists($file)){
		unlink($file);
	}

	// function to append statements to file
	function toFile($data) {
		file_put_contents("sql.sql", $data, FILE_APPEND);
	}

	//drop existing tables
	toFile("DROP TABLE users, roles, websites, user_website, pages; \n");

	// create users table
	// user_id, user_name, user_pwd, user_fname, user_lname, role_id, user_joined_date
	toFile("CREATE TABLE users (user_id integer(2), user_name varchar(50), user_pwd varchar(50), user_fname varchar(50), user_lname varchar(25), role_id integer(1), user_joined_date datetime); \n");
	for ($i=1; $i <= $no_users; $i++) {
	  $role_id = rand(0,4);
	  $customer = "INSERT INTO users VALUES ($i, \"$faker->userName\", \"$faker->word\", \"$faker->firstName\", \"$faker->lastName\", $role_id, CURRENT_TIMESTAMP); \n";
	  toFile($customer);
	}

	// create roles table
	// role_id, role_name
	$roles = ['visitor', 'contributor', 'author', 'administrator', 'super admin'];
	toFile("CREATE TABLE roles (role_id integer(2), role_name varchar(50)); \n");
	foreach ($roles as $i => $r) {
	  $role = "INSERT INTO roles VALUES ($i, \"$r\"); \n";
	  toFile($role);
	}

	// create websites table
	// website_id, website_url, website_timezone
	toFile("CREATE TABLE websites (website_id integer(2), website_url varchar(50), website_timezone varchar(50)); \n");
	for ($i=1; $i<=$no_websites; $i++) {
		$website = "INSERT INTO websites VALUES ($i, \"$faker->domainName\", \"$faker->timezone\"); \n";
		toFile($website);
	}

	// create user_website table
	// user_id, website_id
	toFile("CREATE TABLE user_website (user_id integer(2), website_id integer(2)); \n");
	for ($i=1; $i<=$no_websites; $i++) {
		$user_id = rand(1,$no_users);
		$website_id = rand(1,$no_websites);
		$user_website = "INSERT INTO user_website VALUES ($user_id, $website_id); \n";
		toFile($user_website);
	}

	// create pages table
	// page_id, website_id, page_slug, page_name, page_last_revised, page_parent, page_order 
	toFile("CREATE TABLE pages (page_id integer(2), website_id integer(2), page_slug varchar(100), page_name varchar(100), page_last_revised datetime, page_parent integer(2), page_order integer(2)); \n");
	for ($i=1; $i<=$no_pages; $i++) {
		$website_id = rand(1,$no_websites);
		$page_parent = rand(1, $no_pages);
		$page_name = implode(" ", $faker->words(rand(3,8)));
		$page_order = rand(1,4);
		$pages = "INSERT INTO pages VALUES ($i, $website_id, \"$faker->slug\" ,\"$page_name\", CURRENT_TIMESTAMP, $page_parent, $page_order); \n";
		toFile($pages);
	}

	// create content table
	// content_id, content_version, user_id, content_date, content
	toFile("CREATE TABLE content (content_id integer(2), content_version float(2), user_id integer(2), content_date datetime, content text); \n");
	for ($i=0;$i<=$no_content; $i++) {
		$content_version = rand(0,100)/10;
		$user_id = rand(1, $no_users);
		$content = "INSERT INTO content VALUES ($i, $content_version, $user_id, CURRENT_TIMESTAMP, \"$faker->text\"); \n";
		toFile($content);
	}

	// create settings table
	// setting_id, website_id, setting, setting_value
	toFile("CREATE TABLE settings (setting_id integer(2), website_id integer(2), setting varchar(50), setting_value varchar(50)); \n");
	for ($i=1; $i<=$no_websites*2; $i++) {
		$website_id = ceil($i/2);
		$options = ['image_height', 'image_width'];
		$options_value = ['400', '300'];
		$options = $i%2 == 0 ? $options[0] : $options[1];
		$options_value = $i%2 == 0 ? $options_value[0] : $options_value[1];
		$settings = "INSERT INTO settings VALUES ($i, $website_id, \"$options\" ,\"$options_value\"); \n";
		toFile($settings);
	}

	echo "File successfully made \n";