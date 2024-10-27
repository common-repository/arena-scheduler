// Select.jsx
import { useEffect, useState } from "react";
import Dropdown from "react-bootstrap/Dropdown";
import ButtonGroup from "react-bootstrap/ButtonGroup";

const Select = ({ data, label, variant, bgColor, textColor, onOptionSelect }) => {
	const [selected, setValue] = useState("");

	const handleSelect = (eventKey, event) => {
		// Access the custom data attribute 'data-id'
		const selectedItemID = event.target.getAttribute("data-id");
		onOptionSelect(selectedItemID);

		// Call the parent's function with the selected value (data-id)
		setValue(eventKey);
	};

	// Effect runs when the 'label' state changes
	useEffect(() => {
		setValue(label);
	}, [label, bgColor]); // Dependencies include 'label'

	return (
		<>
			<Dropdown as={ButtonGroup} onSelect={handleSelect}>
				<Dropdown.Toggle
					className={"slot-dropdown-toggle"}
					style={{
						backgroundColor: bgColor ? bgColor : "#fff",
						color: textColor,
						border: '1px solid ' + bgColor
					}}
					variant={variant}
				>
					{selected}
				</Dropdown.Toggle>

				<Dropdown.Menu>
					{data.map((option, index) => (
						<Dropdown.Item
							key={index}
							eventKey={option.name} // Use eventKey for display
							data-id={option.id} // Use data-id for selectedValue
							style={{
								backgroundColor: option.color,
								color: option.text_color,
							}}
						>
							{option.name}
						</Dropdown.Item>
					))}
				</Dropdown.Menu>
			</Dropdown>
		</>
	);
};

export default Select;
