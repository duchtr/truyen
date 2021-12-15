<?php
            $arrnews1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'news',
                    'order'=>'ord asc,id desc',
                    'where'=>'',
                    'limit'=>'0,10',
                    'pivot'=>[]
                )
            );
         ?><?php $countnews1 = count($arrnews1);
     for ($inews1=0; $inews1 < $countnews1; $inews1++) { $itemnews1=$arrnews1[$inews1]; ?>
						<?php echom($itemnews1,'name',1); ?>
					<?php }; ?>