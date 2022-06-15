#!/usr/bin/env bash
filename=database.ini
myArray=(`cat "$filename"`)
declare -A map

for (( i = 0 ; i < ${#myArray[@]} ; i+=3)) do
    map+=([${myArray[$i]}]=${myArray[$i+2]})
done

echo ${map[hostname]}
echo "Please Enter hostname of database:(default-> ${map[dbms_host]})"
read hostname

echo "Please Enter username of database:(default-> ${map[dbms_username]} )"
read username

echo "Please Enter password of database:(default-> ${map[dbms_password]} )"
read password

echo "Please Enter name of database:(default-> ${map[dbms_database]} )"
read name

if [[ -z "$hostname" ]]; then
    hostname=${map[dbms_host]}
fi
if [[ -z "$username" ]]; then
    username=${map[dbms_username]}
fi
if [[ -z "$password" ]]; then
    password=${map[dbms_password]}
fi
if [[ -z "$name" ]]; then
    name=${map[dbms_database]}
fi

rm -rf database.ini
echo dbms_host = $hostname >> database.ini
echo dbms_username = $username >> database.ini
echo dbms_password = $password >> database.ini
echo dbms_database = $name >> database.ini