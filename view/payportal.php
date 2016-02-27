<?php
// this file shows how you can call the CashEnvoy payment interface from your online store

// your CashEnvoy merchant id
$cemertid = 1;

// your merchant key (login to your cashenvoy account, your merchant key is displayed on the dashboard page)
$key = '2dawdszfcasdq24434242ffsdsfd';

// transaction reference which must not contain any special characters. Numbers and alphabets only.
$cetxref = 'A11232132133242';

// transaction amount
$ceamt = 6000.00;

// customer id does not have to be an email address but must be unique to the customer
$cecustomerid = 'cyrilsayeh@gmail.com'; 

// a description of the transaction
$cememo = 'Purchase of a DELL 1525';

// notify url - absolute url of the page to which the user should be directed after payment
// an example of the code needed in this type of page can be found in example_requery_usage.php
$cenurl = 'http://www.bosstrader.com.ng/paymentcomplete'; 

// generate request signature
$data = $key.$cetxref.$ceamt;
$signature = hash_hmac('sha256', $data, $key, false);
?>
<body onLoad="document.submit2cepay_form.submit()">
<!-- 
Note: Replace https://www.cashenvoy.com/sandbox/?cmd=cepay with https://www.cashenvoy.com/webservice/?cmd=cepay once you have been switched to the live environment.
-->
<form method="post" name="submit2cepay_form" action="https://www.cashenvoy.com/sandbox/?cmd=cepay" target="_self">
<input type="hidden" name="ce_merchantid" value="<?= $cemertid ?>"/>
<input type="hidden" name="ce_transref" value="<?= $cetxref ?>"/>
<input type="hidden" name="ce_amount" value="<?= $ceamt ?>"/>
<input type="hidden" name="ce_customerid" value="<?= $cecustomerid ?>"/>
<input type="hidden" name="ce_memo" value="<?= $cememo ?>"/>
<input type="hidden" name="ce_notifyurl" value="<?= $cenurl ?>"/>
<input type="hidden" name="ce_window" value="parent"/><!-- self or parent -->
<input type="hidden" name="ce_signature" value="<?= $signature ?>"/>
</form>
</body>