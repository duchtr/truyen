<div class="container">
                <h2 class="title_border"><a href="">Truyện mới cập nhật</a></h2>
                <div class="row">
                       
                    <?php foreach($list_data as $itempro2): ?>
                    <div class="col-lg-4 col-sm-6 col-6 xs_pd_5">
                            <div class="item_story_high row mar_5 d-flex align-items-center">
                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <a href="<?php echom($itempro2,'slug',1); ?>" class="c-img">
                                        <?php echo imgv2($itempro2,'#w#img','-1',1) ; ?>                                    </a>
                                    <?php if($itempro2['status']==0): ?>
                                        <div class="edit">
                                            <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                                            <span>
                                                <?php
            $arrpro_categories4 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'pro_categories',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'id','compare'=>'=','value'=>$itempro2['parent'])),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countpro_categories4 = count($arrpro_categories4);
     for ($ipro_categories4=0; $ipro_categories4 < $countpro_categories4; $ipro_categories4++) { $itempro_categories4=$arrpro_categories4[$ipro_categories4]; ?>
                                                    <?php echom($itempro_categories4,'short_name',1); ?>
                                                <?php }; ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                </div>
                                
                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <div class="text">
                                        <?php  

                                         $tag = explode(',',$itempro2['tag_pro']);
                                         $id_tag = $tag[0];
                                          ?>
                                        <h3 class="name"><a href="<?php echom($itempro2,'slug',1); ?>" title="<?php echom($itempro2,'name',1); ?>"><?php echom($itempro2,'name',1); ?></a></h3>

                                        <span class="type"><?php
            $arrtag_pro1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'tag_pro',
                    'order'=>'',
                    'where'=>array(array('key'=>'id','compare'=>'=','value'=>$id_tag)),
                    'limit'=>'0,6',
                    'pivot'=>[]
                )
            );
         ?><?php $counttag_pro1 = count($arrtag_pro1);
     for ($itag_pro1=0; $itag_pro1 < $counttag_pro1; $itag_pro1++) { $itemtag_pro1=$arrtag_pro1[$itag_pro1]; ?>
                                        <a href="tags/<?php echom($itemtag_pro1,'link',1); ?>" title="<?php echom($itemtag_pro1,'name',1); ?>"><?php echom($itemtag_pro1,'name',1); ?></a>
                                    <?php }; ?></span>
                                    <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                        <input type="hidden" name="pid" value="<?php echom($itempro2,'id',1); ?>">
                                        <input type="hidden" name="table" value="pro">
                                        <?php 
                                            $arr=$itempro2['score'];
                                            $r = json_decode($arr,true);
                                            $r = (array)$r;
                                            $score = 0;
                                            $total= 0;
                                            foreach($r as $k => $v)
                                            {
                                                $score+=$v* str_replace("s-", "", $k);
                                                $total += $v;
                                            }
                                            $lastScore = $score/($total>0?$total:1);
                                            $w=$lastScore*100/5;
                                             ?>
                                            <div class="star-base">
                                                <div class="star-rate" style="width:<?php  echo $w  ?>%"></div>
                                                <a dt-value="1" href="#1"></a>
                                                <a dt-value="2" href="#2"></a>
                                                <a dt-value="3" href="#3"></a>
                                                <a dt-value="4" href="#4"></a>
                                                <a dt-value="5" href="#5"></a>
                                            </div>
                                        </div>
                                        <a href="<?php echom($itempro2,'slug',1); ?>" class="view_more v2">Xem thêm</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>