import { useEffect } from "react";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import { fetchArenaCategoryData } from "../../utils/api";

// Component to display arena categories as colored buttons
const CategoryView = ({ categories, setCategories }) => {

	useEffect(() => {
		// Fetch arena category data from the API on component mount
		const fetchData = async () => {
			try {
				const data = await fetchArenaCategoryData();
				setCategories(data);
			} catch (error) {
				console.error("Error fetching arena category data:", error);
			}
		};

		fetchData();
	}, []);

	return (
		<div className={"container-fluid text-center"}>
			<Row>
				{/* Mapping through arena category data to create colored buttons */}
				{categories.map((category) => {
					// Style for the colored buttons based on category color
					const buttonStyle = {
						backgroundColor: category.color,
						color: category.text_color,
						border: "none",
						padding: "10px",
					};

					return (
						<Col
							variant="secondary"
							key={"cat_button_" + category.id}
							style={buttonStyle}
						>
							{/* Displaying category name */}
							{category.name}
						</Col>
					);
				})}
			</Row>
		</div>
	);
};

export default CategoryView;
