<?php $type=$type??'info'; $class=match($type){'success'=>'nova-alert-success','warning'=>'nova-alert-warning','error'=>'nova-alert-danger',default=>'nova-alert-info'}; ?>
<div class="nova-alert <?= $class ?>" role="<?= $type==='error'?'alert':'status' ?>"><?= htmlspecialchars((string)($message??''),ENT_QUOTES,'UTF-8') ?></div>
