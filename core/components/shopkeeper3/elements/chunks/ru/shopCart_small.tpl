<div class="shop-cart" data-shopcart="1">
    <div class="shop-cart-head"><b>Корзина</b></div>
    <div class="empty">
        <div class="shop-cart-empty">Пусто</div>
    </div>
</div>
<!--tpl_separator-->
<div class="shop-cart" data-shopcart="1">
    <div class="shop-cart-head"><b>Корзина</b></div>
    <div class="full">
        <div  style="text-align:right;">
            <a href="[[+empty_url]]" id="shk_butEmptyCart">Очистить корзину</a>
        </div>
        <div class="shop-cart-body">Выбрано: <b>[[+items_total]]</b> [[+plural]]</div>
        <div style="text-align:right;">Общая сумма: <b>[[+price_total]]</b> [[+currency]]
        </div>
        <div class="cart-order">
            <a href="[[+order_page_url]]" id="shk_butOrder">Оформить заказ</a>
        </div>
    </div>
</div>