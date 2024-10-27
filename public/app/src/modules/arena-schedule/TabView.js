import { useEffect } from "react";
import Tab from "react-bootstrap/Tab";
import Tabs from "react-bootstrap/Tabs";
import { fetchArenaData } from "../../utils/api";

const TabView = ({ arena, setArena, handleTabSelect }) => {

	// Fetching arena data on component mount
	useEffect(() => {
		// Fetch arena data from the API
		const fetchData = async () => {
			try {
				const data = await fetchArenaData();
				setArena(data);

				// If data is fetched, select the first tab
				if (data.length > 0) {
					handleTabSelect(data[0].id);
				}
			} catch (error) {
				console.error("Error fetching arena data:", error);
			}
		};

		fetchData();
	}, []);

	return (
		<>
			{/* Displaying arena data in tabs */}
			<Tabs
				className={"container-fluid arena-scheduler-tab"}
				onSelect={handleTabSelect}
			>
				{/* Mapping through fetched arena data to create tabs */}
				{arena.map((arena) => (
					<Tab
						key={arena.id}
						eventKey={arena.id}
						title={arena.name}
					></Tab>
				))}
			</Tabs>
		</>
	);
};

export default TabView;
