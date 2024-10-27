import "../../style.css";
import moment from "moment";
import TabView from "./TabView";
import TableView from "./TableView";
import CategoryView from "./CategoryView";
import { useEffect, useState } from "react";

function Calendar() {
	const [wpUserLoggedIn, setHasBodyClass] = useState(false);
	const [activeTab, setActiveTab] = useState(0);
	const [currentWeek, setCurrentWeek] = useState(moment());
	const [arena, setArena] = useState([]);
	const [categories, setCategories] = useState([]);

	const handleTabSelect = (tabId) => {
		// Update active tab when a new tab is selected
		setActiveTab(tabId);
	};

	useEffect(() => {
		const checkBodyClass = () => {
			if (document.body.classList.contains("logged-in")) {
				setHasBodyClass(true);
			} else {
				setHasBodyClass(false);
			}
		};

		checkBodyClass();
	}, []);

	return (
		<div className={wpUserLoggedIn ? "admin-view" : "public-view"}>
			<TabView
				arena={arena}
				setArena={setArena}
				handleTabSelect={handleTabSelect}
			/>

			{activeTab > 0 && (
				<CategoryView
					categories={categories}
					setCategories={setCategories}
				/>
			)}

			{activeTab > 0 && (
				<TableView
					wpUserLoggedIn={wpUserLoggedIn}
					activeTab={activeTab}
					currentWeek={currentWeek}
					setCurrentWeek={setCurrentWeek}
					categories={categories}
					arena={arena}
				/>
			)}
		</div>
	);
}

export default Calendar;
