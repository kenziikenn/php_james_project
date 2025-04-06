<?php   

class Databases{  

      public $con;  
      public $error;  
      public function __construct()  
      {  
           $this->con = mysqli_connect("localhost","root", "", "ajs_db1");  
           if(!$this->con)  
           {  
                echo 'Database Connection Error!!!' . mysqli_connect_error($this->con);  
           }  
      }  
      public function insert($table_name, $data)  
      {  
           $string = "INSERT INTO ".$table_name." (";            
           $string .= implode(",", array_keys($data)) . ') VALUES (';            
           $string .= "'" . implode("','", array_values($data)) . "')";  
           if(mysqli_query($this->con, $string))  
           {  
                return true;  
           }  
           else  
           {  
                echo mysqli_error($this->con);  
           }
           
           mysqli_close($this->con);
           
      }

    public function insertignore($table_name, $data)  
      {  
           $string = "INSERT IGNORE INTO ".$table_name." (";            
           $string .= implode(",", array_keys($data)) . ') VALUES (';            
           $string .= "'" . implode("','", array_values($data)) . "')";  
           if(mysqli_query($this->con, $string))  
           {  
                return true;  
           }  
           else  
           {  
                echo mysqli_error($this->con);  
           }
           
           mysqli_close($this->con);
           
      }
    public function sqlupdate($table_name, $data,$table_field,$compare,$field_value)  
      {  
           	$cols = array();

			foreach($data as $key=>$val) {
				$cols[] = "$key = '$val'";
			}
			$sql = "UPDATE $table_name SET " . implode(', ', $cols) . " WHERE $table_field $compare '".$field_value."'";
		 
			if(mysqli_query($this->con, $sql))  
			   {  
					return true;  
			   }  
			   else  
			   {  
					echo mysqli_error($this->con);  
			   }
			   
			   mysqli_close($this->con);
           
      }
      public function sqlupdateall($table_name, $data)  
      {  
           	$cols = array();

			foreach($data as $key=>$val) {
				$cols[] = "$key = '$val'";
			}
			$sql = "UPDATE $table_name SET " . implode(', ', $cols);
		 
			if(mysqli_query($this->con, $sql))  
			   {  
					return true;  
			   }  
			   else  
			   {  
					echo mysqli_error($this->con);  
			   }
			   
			   mysqli_close($this->con);
           
      }
      public function ssqlupdate($table_name, $data,$table_field1,$compare1,$field_value1,$table_field2,$compare2,$field_value2,$table_field3,$compare3,$field_value3)  
      {  
           	$cols = array();

			foreach($data as $key=>$val) {
				$cols[] = "$key = '$val'";
			}
			$sql = "UPDATE $table_name SET " . implode(', ', $cols) . " WHERE $table_field1 $compare1 '".$field_value1."' AND $table_field2 $compare2 '".$field_value2."' AND $table_field3 $compare3 '".$field_value3."'";
		 
			if(mysqli_query($this->con, $sql))  
			   {  
					return true;  
			   }  
			   else  
			   {  
					echo mysqli_error($this->con);  
			   }
			   
			   mysqli_close($this->con);
           
      }

      public function sssqlupdate($table_name, $data,$table_field1,$compare1,$field_value1,$table_field2,$compare2,$field_value2)  
      {  
           	$cols = array();

			foreach($data as $key=>$val) {
				$cols[] = "$key = '$val'";
			}
			$sql = "UPDATE $table_name SET " . implode(', ', $cols) . " WHERE $table_field1 $compare1 '".$field_value1."' AND $table_field2 $compare2 '".$field_value2."'";
		 
			if(mysqli_query($this->con, $sql))  
			   {  
					return true;  
			   }  
			   else  
			   {  
					echo mysqli_error($this->con);  
			   }
			   
			   mysqli_close($this->con);
           
      }
      public function updatesql($table_name, $data,$table_field,$compare,$field_value,$table_field2,$compare2,$field_value2)  
      {  
           	$cols = array();

			foreach($data as $key=>$val) {
				$cols[] = "$key = '$val'";
			}
			$sql = "UPDATE $table_name SET " . implode(', ', $cols) . " WHERE $table_field $compare '".$field_value."' AND $table_field2 $compare2 '".$field_value2."'";
		 
			if(mysqli_query($this->con, $sql))  
			   {  
					return true;  
			   }  
			   else  
			   {  
					echo mysqli_error($this->con);  
			   }
			   
			   mysqli_close($this->con);
           
      }


	  public function updateaccount($table_name, $data,$table_field,$compare,$field_value)  
      {  
           	$cols = array();

			foreach($data as $key=>$val) {
				$cols[] = "$key = '$val'";
			}
			$sql = "UPDATE $table_name SET activated=1, " . implode(', ', $cols) . " WHERE $table_field $compare '".$field_value."'";
		 
			if(mysqli_query($this->con, $sql))  
			   {  
					return true;  
			   }  
			   else  
			   {  
					echo mysqli_error($this->con);  
			   }
			   
			   mysqli_close($this->con);
           
      }
      public function sqldelete($table_name,$table_field,$compare,$field_value)  
      {  
           $array = array();  
           $query = "DELETE FROM ".$table_name." WHERE ".$table_field." ".$compare." '$field_value'";  
           $result = mysqli_query($this->con, $query);  

      }
      public function deletesql($table_name,$table_field,$compare,$field_value,$table_field2,$compare2,$field_value2)  
      {  
           $array = array();  
           $query = "DELETE FROM ".$table_name." WHERE ".$table_field." ".$compare." '$field_value' AND $table_field2 $compare2 '$field_value2'";  
           $result = mysqli_query($this->con, $query);  

      }

      public function deleteaccount($table_name,$table_field,$compare,$field_value)  
      {  
           $array = array();  
           $query = "DELETE FROM ".$table_name." WHERE ".$table_field." ".$compare." '$field_value'";  
           $result = mysqli_query($this->con, $query);  

      } 
      public function select($table_name,$table_field,$compare,$field_value)  
      {  
           $array = array();  
           $query = "SELECT * FROM ".$table_name." WHERE ".$table_field." ".$compare." '$field_value'";  
           $result = mysqli_query($this->con, $query);  
           while($row = mysqli_fetch_assoc($result))  
           {  
                $array[] = $row;  
           }
           mysqli_close($this->con);
           
           return $array;  
      }  
	  
	  
	  public function registeredaccounts($table_name,$table_field,$compare,$field_value1)  
      {  
           $array = array();  
           $query = "SELECT * FROM ".$table_name." WHERE ".$table_field." ".$compare." '$field_value1%' AND activated=1";  
           $result = mysqli_query($this->con, $query);  
           while($row = mysqli_fetch_assoc($result))  
           {  
                $array[] = $row;  
           }
           mysqli_close($this->con);
           
           return $array;  
      }  
      //complex select statement
      public function sselect($sql)  
      {  
           
           $array = array();  
           $query = $sql;  
           $result = mysqli_query($this->con, $query);  
           while($row = mysqli_fetch_assoc($result))  
           {  
                $array[] = $row;  
           
           }
           mysqli_close($this->con);
           
           return $array;  
      }  
      //return number of rows in a query
      public function getrows($sql)  
      {  
           
           $array = array();  
           $query = $sql;  
           $result = mysqli_query($this->con, $query);  
           $rowcount=mysqli_num_rows($result);
           mysqli_close($this->con);
           
           return $rowcount;  
      }  
      public function selectorder($table_name,$orderfield,$style)  
      {  
           $array = array();  
           $query = "SELECT * FROM ".$table_name." ORDER BY ".$orderfield." ".$style;  
           $result = mysqli_query($this->con, $query);  
           while($row = mysqli_fetch_assoc($result))  
           {  
                $array[] = $row;  
           }
           mysqli_close($this->con);
           
           return $array;  
      } 
      
	  
	   //with username
	   public function selectlogin($table_name,$table_field,$compare,$field_value,$table_field2,$compare2,$field_value2)  
      {  
           $array = array();  
           $query = "SELECT * FROM ".$table_name." WHERE ".$table_field." ".$compare." '$field_value'  AND ".$table_field2." ".$compare2." md5('".$field_value2."')";  
           $result = mysqli_query($this->con, $query);  
           while($row = mysqli_fetch_assoc($result))  
           {  
                $array[] = $row;  
           }
           mysqli_close($this->con);
           
           return $array;  
      }

      public function multiselect($table_name,$table_field,$compare,$field_value,$table_field2,$compare2,$field_value2)  
      {  
           $array = array();  
           $query = "SELECT * FROM ".$table_name." WHERE ".$table_field." ".$compare." '$field_value' AND ".$table_field2." ".$compare2." '$field_value2'";  
           $result = mysqli_query($this->con, $query);  
           while($row = mysqli_fetch_assoc($result))  
           {  
                $array[] = $row;  
           }
           mysqli_close($this->con);
           
           return $array;  
      }
      
      public function execute($string)  
      {  
           if(mysqli_query($this->con, $string))  
           {  
                return true;  
           }  
           else  
           {  
                echo mysqli_error($this->con);  
           }
           
           mysqli_close($this->con);
           
      }
      
	  
	  public function update($table_name,$data,$table_field,$compare,$field_value)  
      {  
          $numOfFields = count($data); 
          $columnName = array_keys($data);  
          $valueToInsert = array_values($data);
          $string = "UPDATE ".$table_name." SET ";   
 
           for ($i=0; $i < $numOfFields; $i++) { 
            $string .= $columnName[$i] ."=". $valueToInsert[$i];
              if($i != $numOfFields-1)
                $string .= " , ";
           }

           $string .= " WHERE ".$table_field." ".$compare." '$field_value'"; 

           if(mysqli_query($this->con, $string))  
           {  
                return true;  
           }  
           else  
           {  
                echo mysqli_error($this->con);  
           }
           
            mysqli_close($this->con);
      }
      
 }  