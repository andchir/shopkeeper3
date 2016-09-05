
============================================

Shopkeeper 3.x

http://modx-shopkeeper.ru/

============================================

Snippet Shopkeeper

Properties:

lang - language. Default - ru.
prodCont - CSS-selector element, inside which there is information about the product (default `div.shk-item`);
cartTpl - Shopping cart chunk name. Default - shopCart.
cartRowTpl - Shopping cart row item cunk name Default - shopCartRow.
packageName - Package name of product table. Example: "shop". Default is empty (modResource).
className - Class name of product table. Example: "ShopContent". Default is empty (modResource).
fieldPrice - Field name or TV name of product price. Default - price.
fieldName - Field name or TV name of product name. Default - pagetitle.
getUnpublished - Allow get unpublished products. Default - false.
allowFloatCount - Allow float for products count. Default - false.
excepDigitGroup - Divide price digits on groups.
orderFormPageId - ID of order form page.
currency - currency of products. Default - руб.
processParams - Process TV of price to output type.
savePurchasesFields - Field List of goods that need to keep in order.
orderDataRowTpl - Chunk line item in the list in a letter that is sent when ordering ([[+orderOutputData]]). Default orderDataRow.
flyToCart - The effect of adding an item to cart - helper | image | nofly (default `helper`);
noJavaScript - Disable JavaScript (default `0`);
noJQuery - No load jquery.js (default `0`);
style - Style of shopping cart (default `default`);

Example use:

[[!Shopkeeper@cart_catalog]]

cart_catalog - Name of property set.

You can output two or more carts on the one page.

Example chunk shopping cart:

<div class="shop-cart" data-shopcart="1">
    <div class="shop-cart-head"><b>Shopping cart</b></div>
    <div class="empty">
        <div class="shop-cart-empty">Empty</div>
    </div>
</div>
<!--tpl_separator-->
<div class="shop-cart" data-shopcart="1">
    <div class="shop-cart-head"><b>Shopping cart</b></div>
    <div class="full">
        <div  style="text-align:right;">
            <a href="[[+empty_url]]" id="shk_butEmptyCart">Clear cart</a>
        </div>
        <div class="shop-cart-body">Selected: <b>[[+items_total]]</b> [[+plural]]</div>
        <div style="text-align:right;">Total price: <b>[[+price_total]]</b> [[+currency]]
        </div>
        <div class="cart-order">
            <a href="[[+order_page_url]]" id="shk_butOrder">Checkout</a>
        </div>
    </div>
</div>

A chunk is composed of two parts separated by a special delimiter <!--tpl_separator-->

The first part - chunk empty basket of goods.
The second part - chunk baskets with goods.

data-shopcart="1" - is the mark, which is determined by a set of parameters for AJAX-updating of the cart.
In the system settings (System Settings -> shopkeeper3) In the parameter "shk3.property_sets" you need to specify the names of the parameter sets (can be somewhat separated by commas)
which are used on your website to the snippet Shopkeeper. It is necessary to synchronize with the AJAX-updating of the cart.
Example shk3.property_sets = cart_catalog,cart_order_page
In this case, the chunk (cartTpl), which is specified in the parameter set "cart_catalog" need to put a label data-shopcart="1".

And chunk that is specified in the parameter set "cart_order_page" put a mark data-shopcart="2" (the serial number of the set of parameters).


============================================

Product chunk example:

<div class="product shk-item">
    <div class="product-b">
        <div class="product-descr">
            <a href="[[~[[+id]]? &scheme=`abs`]]">
                <img class="shk-image" src="[[+tv.image]]" alt="" height="130" width="130" />
            </a>
            <h3>[[+pagetitle]]</h3>
            [[+introtext]]<br />
            <a href="[[~[[+id]]? &scheme=`abs`]]">Details &rsaquo;</a>
            <div style="clear:both;"></div>
        </div>
        <form action="[[~[[*id]]? &scheme=`abs`]]" method="post">
            <fieldset>
                <input type="hidden" name="shk-id" value="[[+id]]" />
                <input type="hidden" name="shk-count" value="1" />
                <div class="product-price">
                    <button type="submit" class="shk-but">Add to cart</button>
                    <div>Price: <span class="shk-price">[[+tv.price:num_format]]</span> руб.</div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

============================================

Products list:

[[!getPage?
&elementClass=`modSnippet`
&element=`getProducts`
&className=`shopContent`
&packageName=`shop`
&limit=`10`
&tpl=`product`
&where=`{"template":15}`
]]
<br clear="all" />
<ul class="pages">
[[!+page.nav]]
</ul>

============================================

Ordering:

[[!FormIt?
&hooks=`spam,shk_fihook,email,FormItAutoResponder,redirect`
&submitVar=`order`
&emailTpl=`shopOrderReport`
&fiarTpl=`shopOrderReport`
&emailSubject=`In the online store "[[++site_name]]" maked a new order`
&fiarSubject=`You have made an order in the online store "[[++site_name]]"`
&emailTo=`[[++emailsender]]`
&fiarReplyTo=`[[++emailsender]]`
&fiarToField=`email`
&redirectTo=`10`
&validate=`address:required,fullname:required,email:email:required,phone:required`
&errTpl=`<br /><span class="error">[[+error]]</span>`
]]

