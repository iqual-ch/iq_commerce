class RelatedProducts extends React.Component {
  constructor(props) {
    super(props);
  }

  addToCart() {
    console.log("OK");
  }


  render(e) {
    return (
      <div className="related-products-content container-fluid">
        <div className="iq-row">
          <div className="col">

            <RelatedProductsClose />
            <h3>Zusatzleistung</h3>
            <ul className="items">


              {Object.keys(this.props.items).map((item, key) => {
                return <li className="item"
                  key={key}>


                  <div className="item-header">
                    {this.props.items[item].title}
                  </div>

                  <div className="item-body">
                    <div className="variations">
                      <input name="quantity" type="number" min="0" />
                      <select>
                        {this.props.items[item].variations.map((option, key) => {
                          return <option key={key} value={option.variation_id}>{option.title}</option>
                        })}
                      </select>
                      <button className="add-one" onClick={e => { this.addToCart() }} aria-label="In den Warenkorb legen" type="button" ><i className="fas fa-cart-plus"></i></button>
                    </div>
                  </div>
                </li>
              })}
            </ul>

          </div>
        </div>

      </div>
    );
  }
}



class RelatedProductsClose extends React.Component {
  constructor(props) {
    super(props);
  }

  closeProductSuggestions(){
    jQuery('.related-products-overlay').remove();
  }

  render(e) {
    return (
      <button onClick={e => { this.closeProductSuggestions() }}><i className="fas fa-times"></i></button>
    );
  }
}
