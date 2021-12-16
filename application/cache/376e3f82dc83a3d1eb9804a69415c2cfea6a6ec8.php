<footer>
    <div class="container text-center">
        <a href="<?php  base_url();  ?>" class="logo-nav m-auto cursor" title="<?php  base_url();  ?>">
            <img src="https://www.pngmart.com/files/21/Open-Book-PNG-Pic.png"/>
        </a>
        <div class="mxh">
            <a href="" class="smooth">
                <i class="fa fa-facebook" aria-hidden="true"></i>
            </a>
            <a href="" class="smooth">
                <i class="fa fa-youtube" aria-hidden="true"></i>
            </a>
            <a href="" class="smooth">
                <i class="fa fa-twitter" aria-hidden="true"></i>
            </a>
            <a href="" class="smooth">
                <i class="fa fa-skype" aria-hidden="true"></i>
            </a>
        </div>
        <div class="menu_footer">
            <?php
            $arrmenu2 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'menu',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'group_id','compare'=>'=','value'=>'2')),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countmenu2 = count($arrmenu2);
     for ($imenu2=0; $imenu2 < $countmenu2; $imenu2++) { $itemmenu2=$arrmenu2[$imenu2]; ?>
            <a href="<?php echom($itemmenu2,'link',1); ?>" title="<?php echom($itemmenu2,'name',1); ?>" class="smooth"><?php echom($itemmenu2,'name',1); ?></a>
            <?php }; ?>
        </div>
        <div class="copy_right">
            <span><?php echo $this->CI->Dindex->getSettings('COPYRIGHT'); ?></span>
        </div>
    </div>
</footer>