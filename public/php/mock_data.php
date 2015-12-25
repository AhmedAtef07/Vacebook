<?php

require 'db_connect.php';

$response['succeeded'] = false;
$response['logined'] = false;

$password = sha1('12345');

$query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, phone_number, hometown, marital_status, about_me)
  VALUES ('ahmedAtef',
    'Ahmed',
    'Atef',
    'Male',
    '1994-05-25',
    'ahmedatef07@gmail.com',
    ?,
    '01111722255',
    'Alexandria, egypt',
    'Single',
    'blatter Natrix uneath recrease mulk trimyristin outland cobblery marshbuck Hondurean overdilute inhaler hyperflexion sparable undefensed inspectorate protocoleopterous conditioned Confervales penning eremitic superaccrue hydrocephalic barbeiro');");
$query->bind_param('s', $password);
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}

$query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, phone_number, hometown, marital_status, about_me)
  VALUES ('mahmoudHammam',
    'Mahmoud',
    'Hammam',
    'Male',
    '1995-05-25',
    'mahmoudHammam@gmail.com',
    ?,
    '01111722255',
    'Els3ed, egypt',
    'Single',
    'autotypic neurophysiology sourjack brazenface overterrible Negritize roadless heater galleass endemiological neuroparalysis retentor should Slavophobe stimulator pseudoacademical goatherdess nonopposition coupled sawmilling primatal spokesmanship Fuchsian guruship');");

$query->bind_param('s', $password);
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}

$query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, phone_number, hometown, marital_status, about_me)
    VALUES ('ahmedEmad',
      'Ahmed',
      'Emad',
      'Male',
      '1994-11-10',
      'ahmedEmad94@gmail.com',
      ?,
      '01010899181',
      'Alexandria, egypt',
      'Single',
      'blatter Natrix uneath recrease mulk trimyristin outland cobblery marshbuck Hondurean overdilute inhaler hyperflexion sparable undefensed inspectorate protocoleopterous conditioned Confervales penning eremitic superaccrue hydrocephalic barbeiro');");

$query->bind_param('s', $password);
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
} else {
  $response['succeeded'] = true;
  $response['logined'] = true;
  $_SESSION['user_id'] = $query_insert_id;
}

$query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, phone_number, hometown, marital_status, about_me)
      VALUES ('ahmedMagdy',
        'Ahmed',
        'Magdy',
        'Male',
        '1993-05-25',
        'ahmedMagdy@gmail.com',
        ?,
        '01212251067',
        'Cairo, egypt',
        'Single',
        'react imperation haptotropic pearceite bycoket chirpiness solarization surfrappe dewclaw hemogenic Coccothraustes Podophthalmia revelry colletic Cladodontidae diactinism peregrin meteorite excurvate uncropped spermophyte Ods dalmatic enchantingly');");

$query->bind_param('s', $password);
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}

$query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, hometown, marital_status, about_me)
        VALUES ('Shrein',
                'Shrein',
                'Shrein',
                'Female',
                '1996-10-20',
                'Shrein@gmail.com',
                ?,
                'Alexandria, egypt',
                'Single',
                'fibrinose schillerization doughnut sobralite prison clownery pupoid deutoxide Rhapis acetophenetide medicotopographic northfieldite wickedish unpeace pregnantness stipendiary preconceived remotive wardable nephrocolopexy subordinationism spalpeen underhanded elfland');");

$query->bind_param('s', $password);
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}



$query = conn()->prepare("INSERT INTO posts (user_id, caption)
          VALUES (1,
                  'Post 1 From User 1');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}

$query = conn()->prepare("INSERT INTO posts (user_id, caption)
            VALUES (2,
                    'Post 1 From User 2');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}
$query = conn()->prepare("INSERT INTO posts (user_id, caption)
            VALUES (3,
                    'Post From User 3');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}
$query = conn()->prepare("INSERT INTO posts (user_id, caption)
            VALUES (5,
                    'Post 1 From User 5');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}



$query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
            VALUES (1,
                    2,
                    'user 2 wrote comment 1 on post from user 1');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}
$query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
            VALUES (1,
                    3,
                    'user 3 wrote comment 2 on post from user 1');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}
$query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
            VALUES (1,
                    1,
                    'user 1 wrote comment 3 on post from user 1');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}
$query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
            VALUES (2,
                    2,
                    'user 2 wrote comment 1 on post from user 2');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}
$query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
            VALUES (2,
                    4,
                    'user 4 wrote comment 2 on post from user 2');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}
$query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
            VALUES (3,
                    3,
                    'user 3 wrote comment 1 on post from user 5');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}
$query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
            VALUES (3,
                    4,
                    'user 4 wrote comment 1 on post from user 5');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}
$query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
            VALUES (3,
                    5,
                    'user 5 wrote comment 1 on post from user 5');");
$query->execute();
$query_insert_id = $query->insert_id;
$query->close();
if ($query_insert_id == 0) {
    echo 'Error';
}

echo json_encode($response);

?>
