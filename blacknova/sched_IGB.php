<?

  if (preg_match("/sched_IGB.php/i", $PHP_SELF)) {
      echo "You can not access this file directly!";
      die();
  }

  echo "<B>IBANK</B><BR><BR>";
  $ibank_result = $db->Execute("SELECT * from $dbtables[ibank_accounts]");
  $num_accounts = $ibank_result->RecordCount();

  if($num_accounts > 0)
  {
    for($i=1; $i<=$num_accounts ; $i++)
    {
	    $account = $ibank_result->fields;
	    // Check if the user actually has a balance on his acount
	    if($account[balance] > 0)
	    {
		    // Calculate Interest
		    $interest = round($ibank_interest * $account[balance]);
		    // Update users bank account
		    $db->Execute("UPDATE $dbtables[ibank_accounts] SET balance = balance + $interest WHERE ship_id = $account[ship_id]");
		    // Check if the user has a loan
		  }

      if($account[loan] > 0)
      {
  	    $linterest = round($ibank_loaninterest * $account[loan]);
		    $db->Execute("UPDATE $dbtables[ibank_accounts] SET loan = loan + $linterest WHERE ship_id = $account[ship_id]");
	    }

	    echo "ID: $account[ship_id] Balance: $account[balance] Interest: $interest - Loan: $account[loan] Interest on loan: $linterest<br>\n";
      $ibank_result->MoveNext();
    }
  }

?>