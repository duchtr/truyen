<footer>
    <div class="container text-center">
        <a href="{@ base_url(); @}" class="logo-nav m-auto cursor" title="{@ base_url(); @}">
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
            <!--DBS-loop.menu.2|where:act = 1,group_id = 2|order:|limit:-->
            <a href="{(itemmenu2.link)}" title="{(itemmenu2.name)}" class="smooth">{(itemmenu2.name)}</a>
            <!--DBE-loop.menu.2-->
        </div>
        <div class="copy_right">
            <span>{[COPYRIGHT]}</span>
        </div>
    </div>
</footer>