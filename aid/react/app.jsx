import React from 'react';
import {render} from 'react-dom';
import Slider from 'react-slick';
import { BrowserRouter as Router, Route } from 'react-router-dom';
//import { ProductSlider } from './product_slider.js'

class MyComponent extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            data: []
        };
    }

    componentDidMount() {
        fetch("http://www.aid.com/site-api/sampledata")
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        isLoaded: true,
                        data: result
                    });
                },
                // Note: it's important to handle errors here
                // instead of a catch() block so that we don't swallow
                // exceptions from actual bugs in components.
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            )
    }

    render() {
        const {
            error,
            isLoaded,
            data
        } = this.state;
        if (error) {
            return <div > Error: {
                error.message
            } < /div>;
        } else if (!isLoaded) {
            return <div > Loading... < /div>;
        } else {
            return ( <
                ul > {
                    data.map(obj => ( <
                        li key = {
                            obj.name
                        } > {
                            obj.name
                        } {
                            obj.price
                        } <
                        /li>
                    ))
                } <
                /ul>
            );
        }
    }
}

class MiniCartCount extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            data: []
        };
    }

    componentDidMount() {
        fetch("http://www.aid.com/getMiniCartCount/" + JSON.stringify({
                'user_id': 168
            }))
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        isLoaded: true,
                        data: result
                    });
                }
            )
    }

    render() {
        const {
            error,
            isLoaded,
            data
        } = this.state;
        return ( <
            div className = "cart-item-count" > {
                data.map(item => ( <
                    span key = {
                        item.item_count
                    }
                    className = "data-count" > {
                        item.item_count
                    } < /span>
                ))
            } <
            /div>
        );
    }
}

class ProductAdditionalInfo extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            data: []
        };
    }

    componentDidMount() {
        fetch("http://www.aid.com/site-api/sampledata")
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        isLoaded: true,
                        data: result
                    });
                },
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            )
    }

    render() {
        const {
            error,
            isLoaded,
            data
        } = this.state;
        if (error) {
            return <div > Error: {
                error.message
            } < /div>;
        } else if (!isLoaded) {
            return <div > Loading... < /div>;
        } else {
            return ( 
				<ul> {
                    data.map(obj => ( 
						<li key = {
                            obj.name
                        } > {
                            obj.name
                        } {
                            obj.price
                        } </li>
                    ))
                } </ul>
            );
        }
    }
}

var settings = {
	dots: false,
	autoplay:false,
	infinite: true,
	speed: 500,
	lazyLoad: true,
	centerPadding: '10px',
	slidesToShow: 5,
	slidesToScroll: 1
};

/* best seller collection */
class BestSellerCollection extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            data: []
        };
    }

    componentDidMount() {
		fetch("http://www.aid.com/ws/product-collection")
            .then(res => res.json())
            .then(
                (result) => {
					console.log(result);
                    this.setState({
                        isLoaded: true,
                        data: result
                    });
                },
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            )
    }

    render() {
        const {
            error,
            isLoaded,
            data
        } = this.state;
        if (error) {
            return <div > Error: {
                error.message
            } < /div>;
        } else if (!isLoaded) {
            return (
				<div className="text-center">
					<img className="loader" src="http://img.humo.be/q100/w696/h/img_145/1457322.gif" />
				</div>
			);
        } else {
            return (
				<Slider {...settings}>
				{
					data.map(item =>
						<div className="col-md-4 text-center" key={item.id}>
							<div className="item-header">
								<a href={"/product/view/" + item.id}>
									<img src={item.image} className="img-responsive slider-img" alt=""/>
								</a>
							</div>
							<div className="item-footer">
								<span className="text-center">{item.name}</span>
							</div>
						</div>
					)
				}
				</Slider>
            );
        }
    }
}
/* end: best seller collection */

/* featured product collection */
class FeaturedProductCollection extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            data: []
        };
    }

    componentDidMount() {
		fetch("http://www.aid.com/ws/product-collection")
            .then(res => res.json())
            .then(
                (result) => {
					console.log(result);
                    this.setState({
                        isLoaded: true,
                        data: result
                    });
                },
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            )
    }

    render() {
        const {
            error,
            isLoaded,
            data
        } = this.state;
        if (error) {
            return <div > Error: {
                error.message
            } < /div>;
        } else if (!isLoaded) {
            return (
				<div className="text-center">
					<img className="loader" src="http://img.humo.be/q100/w696/h/img_145/1457322.gif" />
				</div>
			);
        } else {
            return (
				<Slider {...settings}>
				{
					data.map(item =>
						<div className="col-md-4 text-center" key={item.id}>
							<div className="item-header">
								<a href={"/product/view/" + item.id}>
									<img src={item.image} className="img-responsive slider-img" alt=""/>
								</a>
							</div>
							<div className="item-footer">
								<span className="text-center">{item.name}</span>
							</div>
						</div>
					)
				}
				</Slider>
            );
        }
    }
}
/* end: featured product collection */

//render( < MyComponent /> , document.getElementById('app'));
/* render(
    <Router>
		<Route path="/view/:id" component={ProductAdditionalInfo}/>
    </Router>,
    document.getElementById('additional_info')
); */

render( <BestSellerCollection /> , document.getElementById('best-seller-collection'));
render( <FeaturedProductCollection /> , document.getElementById('featured-product-collection'));
render( <MiniCartCount /> , document.getElementById('cart-items'));