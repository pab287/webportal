<?php $dateToday = date("Y-m-d"); ?>
<?php $recordedDate = date("Y-m-d", strtotime($date)); ?>
<?php $notif = ($recordedDate < $dateToday)? "last":"today"; ?>
<p>You are about to tag this person <strong><?php echo $employee_name; ?></strong>
 with a Biometric Number <strong>#<?php echo $biometricno; ?> <?php echo $notif; ?> <?php echo date("F d, Y", strtotime($date)); ?>?</strong></p>