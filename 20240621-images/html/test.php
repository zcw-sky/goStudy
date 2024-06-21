<?php
$arrOrg=[];
$arrBeforTargetUser=[1,2,3];
$arrOrgId=array_keys($arrOrg);
var_dump($arrOrgId);
$arrDiffTag=array_diff($arrBeforTargetUser,$arrOrgId);//先晒出被删除的
var_dump($arrDiffTag);
echo "string";