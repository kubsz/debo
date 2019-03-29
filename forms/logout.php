<?php
setcookie('logsession', null, time() - (14400), '/');
header("Location: /");
?>