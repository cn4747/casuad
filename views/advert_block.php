    <div class="advert">


        <div id="advert-image">
            {advert_block}
            <div class="block">
                <a href="{url}">
                <div class="text">
                    <div class="text1">{text1}</div>
                    <div class="text2">{text2}</div>
                </div>

                <img src="{base_url}uploads/banners/{image}" alt=""/>
                </a>
            </div>
            {/advert_block}
        </div>

        <div class="panel">

            <a class="pause" href="javascript: void(0);">&nbsp;</a>

            <div class="pages">
            </div>

        </div><!-- .panel -->

    </div><!-- .advert -->

    <script type="text/javascript">
        $('#advert-image').cycle({
			fx:      'fade',
			pager: '.advert .pages',
			pagerAnchorBuilder: function(idx, slide) {
                return '<a href="javascript: void(0);">'+(idx + 1)+'</a>';
            }
		});

        var clickFlag = 0;
        $('.pause').click(function() {
            if(clickFlag == 0)
            {
                clickFlag = 1;
                $('#advert-image').cycle('pause');
            }
            else
            {
                clickFlag = 0;
                $('#advert-image').cycle('resume');
            }
        });
    </script>
