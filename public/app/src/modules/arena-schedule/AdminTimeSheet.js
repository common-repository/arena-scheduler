import CommentModal from "./CommentModal";
import { useEffect, useState } from "react";
import Button from "react-bootstrap/Button";
import BookSlotModal from "./BookSlotModal";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCircleInfo } from "@fortawesome/free-solid-svg-icons";
import Select from "../../utils/components/Select";

// Component to manage admin timesheet for a specific slot
const AdminTimeSheet = ({
	activeTab,
	getTimesheetForSlot,
	day,
	slot,
	categories,
	activeArena,
	refreshTimesheetData,
}) => {
	// State to manage the selected category and modal visibility
	const [category, setSelectedCategory] = useState("");
	const [selectedValue, setOptionSelectedValue] = useState("");

	// State to manage the value of the input field
	const [comment, setCommentValue] = useState("");

	// State to manage the visibility of the Book Arena Time Slot Modal
	const [showBookArenaTimeSlotModal, setBookArenaTimeSlotModal] =
		useState(false);

	// State to manage the visibility of the Time Slot Comment Modal
	const [showTimeSlotCommentModal, setTimeSlotCommentModal] = useState(false);

	// Function to close the modal
	const handleCloseBookArenaTimeSlotModal = () =>
		setBookArenaTimeSlotModal(false);
	const handleShowTimeSlotCommentModal = (comment) => {
		// You can use the comment parameter here in your function logic
		setCommentValue(comment);
		setTimeSlotCommentModal(true);
	};
	const handleCloseTimeSlotCommentModal = () =>
		setTimeSlotCommentModal(false);

	// Function to handle category selection changes
	const handleCategoryChange = (event) => {
		// const selectedValue = event.target.value;
		setSelectedCategory(selectedValue);

		// Open the modal when an option is selected
		if (selectedValue !== "") {
			setBookArenaTimeSlotModal(true);
		}
	};

	// Effect runs when the 'showBookArenaTimeSlotModal' state changes and becomes true
	useEffect(() => {
		if (showBookArenaTimeSlotModal === false) {
			setSelectedCategory(""); // Reset the selected category when the modal closes
			setOptionSelectedValue("");
		}
	}, [showBookArenaTimeSlotModal]); // Dependencies include 'showBookArenaTimeSlotModal'

	// Effect runs when the 'comment' state changes
	useEffect(() => {
		// console.log(comment);
	}, [comment]);

	useEffect(() => {
		// If you want to update the local state when selectedValue changes
		if (selectedValue !== "Select") {
			handleCategoryChange(selectedValue);
		}
		// handleCategoryChange(selectedValue);
	}, [selectedValue]);

	return (
		<td>
			{/* Conditional rendering based on timesheet data availability */}
			{getTimesheetForSlot(day, slot) ? (
				<div>
					{/* Dropdown to select category with dynamic background color */}
					<div className={"arena-select-wrapper"}>
						{/* <select
							id={day + slot + "TRUE"}
							className={
								"form-select w-75 text-center select-arena"
							}
							value={category}
							onChange={handleCategoryChange}
							style={{
								backgroundColor: getTimesheetForSlot(day, slot)
									.color,
							}}
						>
							<option key="">
								{getTimesheetForSlot(day, slot).comment}
							</option>
							{categories.map((category, index) => {
								return (
									<option key={index} value={category.id}>
										{category.name}
									</option>
								);
							})}
						</select> */}

						<Select
							data={categories}
							label={getTimesheetForSlot(day, slot).comment}
							variant={""}
							bgColor={getTimesheetForSlot(day, slot).color}
							textColor={getTimesheetForSlot(day, slot).text_color}
							onOptionSelect={setOptionSelectedValue}
						/>

						<Button
							variant="link"
							className={"px-2 add-comment-button"}
							onClick={() =>
								handleShowTimeSlotCommentModal(
									getTimesheetForSlot(day, slot).comment
								)
							}
						>
							<FontAwesomeIcon icon={faCircleInfo} />
						</Button>
					</div>
				</div>
			) : (
				<>
					{/* Dropdown to select category */}
					<div className={"arena-select-wrapper"}>
						{/* <select
							id={day + slot + "FALSE"}
							className={
								"form-select w-75 text-center select-arena"
							}
							value={category}
							onChange={handleCategoryChange}
						>
							<option key=""></option>
							{categories.map((category, index) => {
								return (
									<option key={index} value={category.id}>
										{category.name}
									</option>
								);
							})}
						</select> */}

						<Select
							data={categories}
							label={""}
							variant={"secondary"}
							onOptionSelect={setOptionSelectedValue}
						/>

						<Button
							variant="link"
							className={"px-2 add-comment-button"}
							onClick={() => handleShowTimeSlotCommentModal("")}
						>
							<FontAwesomeIcon icon={faCircleInfo} />
						</Button>
					</div>
				</>
			)}

			{/* Render the BookSlotModal if a category is selected */}
			{category !== "" && (
				<BookSlotModal
					showBookArenaTimeSlotModal={showBookArenaTimeSlotModal}
					handleCloseBookArenaTimeSlotModal={
						handleCloseBookArenaTimeSlotModal
					}
					activeTab={activeTab}
					day={day}
					slot={slot}
					category={category}
					activeArena={activeArena}
					refreshTimesheetData={refreshTimesheetData}
				/>
			)}

			{/* Render the CommentModal */}
			<CommentModal
				showTimeSlotCommentModal={showTimeSlotCommentModal}
				handleCloseTimeSlotCommentModal={
					handleCloseTimeSlotCommentModal
				}
				activeTab={activeTab}
				day={day}
				slot={slot}
				category={category}
				refreshTimesheetData={refreshTimesheetData}
				savedComment={comment}
			/>
		</td>
	);
};

export default AdminTimeSheet;
