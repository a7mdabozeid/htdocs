Author: Ali Alanzan
This is a short description


Every folder had 2 PHP files, 
First one resort-data.php
 can be executed to get a specific JSON file and then print the resorted data


Then
Take that sorted value and store it manually in a JSON file


Second one insert_products_data.php
Read a JSON file and insert the products with their variations in the database.
If you need to delete the SKUs that are in the database search in the file for the ( $delete_mode ) variable and set it to true
    when running the program This will delete any product that exists in the database with an existed SKU in the JSON file