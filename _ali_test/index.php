<form action="soap.php" target="my-iframe" method="post">
			
  <label for="text">WSDL URL</label>
  <input type="text" name="url" id="text">
  <br>
	
	  <label for="text">Username</label>
  <input type="text" name="user" id="text">
		 <br>
		
	  <label for="text">Password</label>
  <input type="password" name="pass" id="text">
		 <br>
				
  <input type="submit" value="post">
			
</form>
		
<iframe name="my-iframe" src="soap.php"  style='width:100%;height:500px;'></iframe>