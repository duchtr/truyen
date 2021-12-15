<?php
            $arrnews1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'news',
                    'order'=>'ord asc,id asc',
                    'where'=>array(array('key'=>'id','compare'=>'in','value'=>array(1,4,5))),
                    'limit'=>'0,4',
                    'pivot'=>[]
                )
            );
         ?><?php $countnews1 = count($arrnews1);
     for ($inews1=0; $inews1 < $countnews1; $inews1++) { $itemnews1=$arrnews1[$inews1]; ?>


	<?php echom($itemnews1,'name',1); ?>
	<?php echo imgv2($itemnews1,'#W#img','-1',false) ; ?><?php }; ?>

