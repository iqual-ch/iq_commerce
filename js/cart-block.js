'use strict';

const e = React.createElement;

class MiniCart extends React.Component {
  constructor(props) {
    super(props);
    this.state = { liked: false };
  }


  updateItem(e) {
    console.log(e.target.value);
  }

  // <li key={key}
  // data-product-id={item.purchased_entity.product_id}
  // data-variation-id={item.purchased_entity.variation_id}> - {item.title} - </li>


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
              <button aria-label="Artikel löschen" type="button" className="remove-all"><i className="fas fa-trash-alt"></i></button>
            </div>

            <div className="item-body">
              <div className="item-update">
                <div>
                  <button className="add-one" aria-label="Anzahl -1" disabled="" type="button"><i className="fas fa-minus"></i></button>
                  <input name="quantity" type="number" min="0" aria-label="Eingabe der Menge" value={Math.round(item.quantity)} onChance={updateItem} />
                  <button className="remove-one" aria-label="Anzahl +1" type="button" ><i className="fas fa-plus"></i></button>
                </div>
              </div>
              <strong className="item-price">{item.total_price.formatted}</strong>
            </div>
          </div>
        </li>

      })}


    </ul>
    );
  }

  // render() {
  //   if (this.state.liked) {
  //     return 'You liked this.';
  //   }

  //   // return e(
  //   //   'button',
  //   //   { onClick: () => this.setState({ liked: true }) },
  //   //   'Like'
  //   // );
  // }
}


function updateItem(e){
  console.log(e.target.value);
}
