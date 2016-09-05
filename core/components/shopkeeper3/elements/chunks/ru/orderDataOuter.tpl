
<p><b>Состав заказа</b></p>

<table>
    <thead>
        <tr class="active">
            <th>Наименование</th>
            <th>Параметры</th>
            <th>Кол-во, шт.</th>
            <th>Цена, [[+currency]]</th>
        </tr>
    </thead>
    <tbody>
        [[+purchases]]
    </tbody>
    <tfoot>
        <tr class="cart-order">
            <td colspan="3" style="text-align: right;">
                [[+delivery]]
            </td>
            <td>
                <b>[[+delivery_price:num_format]]</b>
            </td>
        </tr>
        <tr class="cart-order">
            <td colspan="3" style="text-align: right;">
                <b>Итого:</b>
            </td>
            <td>
                <b>[[+price:num_format]]</b>
            </td>
        </tr>
    </tfoot>
</table>

<p><b>Контактные данные</b></p>

<table>
    <colgroup>
        <col width="50%" span="2">
    </colgroup>
    <tbody>
        [[+contacts]]
    </tbody>
</table>
