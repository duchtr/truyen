<div class="row">
    @foreach($list_data as $item)
    <div class="col-lg-6 col-12">
        <div class="item_chapter">
            <h2 class="name_chapter item_chapter_detail w50"><a href="{(item.slug)}">{(item.name)}</a></h2>
            <div class="cover_item d-flex justify-content-end align-items-center w50">
                <span class="offline_btn" data-chapter="{(item.name)}" data-chapter-id="{(item.id)}" data-name="{(story.name)}" data-slug="{(story.slug)}?offline=true" data-chapter-slug="{(item.slug)}?offline=true" data-id="{(story.id)}" data-img="[[story.img.350x0]]" ><i class="fa fa-download"></i></span>
                <div class="free_chapter item_chapter_detail">
                    <span>Free</span>
                </div>
                <div class="time_chapter item_chapter_detail w40 text-right">
                    {{date('d/m/Y',$item['create_time'] )}}
                </div>
            </div>
            
        </div>
        
    </div>
    @endforeach                        

</div>
<div class="pagination_t row_xxx">
    {%PAGINATION%}
</div>