<div class="row">
    <?php foreach($list_data as $item): ?>
    <div class="col-lg-6 col-12">
        <div class="item_chapter">
            <h2 class="name_chapter item_chapter_detail w50"><a href="<?php echom($item,'slug',1); ?>"><?php echom($item,'name',1); ?></a></h2>
            <div class="cover_item d-flex justify-content-end align-items-center w50">
                <span class="offline_btn" data-chapter="<?php echom($item,'name',1); ?>" data-chapter-id="<?php echom($item,'id',1); ?>" data-name="<?php echom($story,'name',1); ?>" data-slug="<?php echom($story,'slug',1); ?>?offline=true" data-chapter-slug="<?php echom($item,'slug',1); ?>?offline=true" data-id="<?php echom($story,'id',1); ?>" data-img="<?php echo imgv2($story,'img','350x0',false) ; ?>" ><i class="fa fa-download"></i></span>
                <div class="free_chapter item_chapter_detail">
                    <span>Free</span>
                </div>
                <div class="time_chapter item_chapter_detail w40 text-right">
                    <?php echo e(date('d/m/Y',$item['create_time'] )); ?>

                </div>
            </div>
            
        </div>
        
    </div>
    <?php endforeach; ?>                        

</div>
<div class="pagination_t row_xxx">
    <?php  echo $this->CI->pagination->create_links(); ?>
</div>