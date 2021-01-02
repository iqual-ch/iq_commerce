'use strict';

const e = React.createElement;

class MiniCart extends React.Component {
  constructor(props) {
    super(props);
  }

  removeItem(itemData) {
    Drupal.behaviors.iq_commerce_ajax_cart.getCsrfToken(function (csrfToken) {
      Drupal.behaviors.iq_commerce_ajax_cart.removeFromCart(csrfToken, itemData['commerce_order'], itemData['commerce_order_item']);
    });
  }

  render() {
    return (
    <ul className="items">
      {this.props.items.map((item, key) => {
        return <li className="item"
          key={key}
          data-product-id={item.purchased_entity.product_id}
          data-variation-id={item.purchased_entity.variation_id}>
          <div>
            <div className="item-header">
              <a href="/product/{item.purchased_entity.product_id}" >{item.title}</a>
              <button aria-label="Artikel löschen" type="button" className="remove-all" onClick={() => {this.removeItem({
                commerce_order: item.order_id,
                commerce_order_item: item.order_item_id
              })}}>
                <i className="fas fa-trash-alt"></i>
              </button>
            </div>

            <div className="item-body">
              <div className="item-update">
                <MiniCartCounter value={Math.round(item.quantity)} commerce_order={item.order_id} commerce_order_item={item.order_item_id}/>
              </div>
              <strong className="item-price">{item.total_price.formatted}</strong>
            </div>
          </div>
        </li>
      })}
    </ul>
    );
  }
}

class MiniCartCounter extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      quantity: this.props.value
    }
  }

  updateItemQuantity(e) {
    this.setState({quantity: Math.max(1, e.target.value)}, () => this.updateCartItem());
  }


  increaseItemQuantity(val){
    this.setState({quantity: Math.max(1, this.state.quantity + val)}, () => this.updateCartItem());
  }

	updateCartItem(){
    let self = this;
    Drupal.behaviors.iq_commerce_ajax_cart.getCsrfToken(function (csrfToken) {
      Drupal.behaviors.iq_commerce_ajax_cart.updateItem(csrfToken, self.props.commerce_order, self.props.commerce_order_item, self.state.quantity );
    });
  }

  render(e) {
    return (
      <div>
        <button className="remove-one" onClick={e => {this.increaseItemQuantity(-1)}} aria-label="Anzahl -1" type="button" ><i className="fas fa-minus"></i></button>
        <input name="quantity" type="number" min="0" aria-label="Eingabe der Menge" value={this.state.quantity} onChange={e => {this.updateItemQuantity(e)}} />
        <button className="add-one" onClick={e => {this.increaseItemQuantity(1)}} aria-label="Anzahl +1" type="button" ><i className="fas fa-plus"></i></button>
      </div>
    );
  }
}
