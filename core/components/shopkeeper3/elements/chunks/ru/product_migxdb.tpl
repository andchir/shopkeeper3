<div class="product shk-item">
    <div class="product-b">
        <div class="product-descr">
            <a href="[[~[[+resource_id]]]][[+alias]].html">
                <img class="shk-image" src="[[+image]]" alt="" height="130" width="130" />
            </a>
            <h3>[[+pagetitle]]</h3>
            [[+introtext]]<br />
            <a href="[[~[[+resource_id]]]][[+alias]].html">Подробнее &rsaquo;</a>
            <div style="clear:both;"></div>
        </div>
        <form action="[[~[[*id]]? &scheme=`abs`]]" method="post">
            <fieldset>
                <input type="hidden" name="shk-id" value="[[+id]]" />
                <input type="hidden" name="shk-name" value="[[+pagetitle]]" />
                <input type="hidden" name="shk-count" value="1" />
                <div class="product-price">
                    <button type="submit" class="shk-but">В корзину</button>
                    <div>Цена: <span class="shk-price">[[+price:num_format]]</span> руб.</div>
                </div>
            </fieldset>
        </form>
    </div>
</div>