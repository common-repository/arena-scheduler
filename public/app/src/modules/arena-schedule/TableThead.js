import moment from "moment";

const TableThead = ({ weekNumber, weekDays }) => {
	const currentDate = moment();

	return (
		<thead className={"text-uppercase"}>
			<tr>
				<th className={"th-week-element"}>
					V<span className={"week-element"}>ECKA</span> {weekNumber}
				</th>

				{weekDays.map((day, index) => {
					const formattedDate = moment(day).format("DD/MM");
					const isCurrentDate = moment(day).isSame(
						currentDate,
						"day"
					);
					const headerStyle = isCurrentDate
						? { backgroundColor: "#26263E", color: "#FFFFFF" }
						: {};

					return (
						<th key={index} style={headerStyle}>
							{formattedDate}
						</th>
					);
				})}
			</tr>

			<tr>
				<th>Time</th>
				<th>
					M<span className={"day-element"}>ÅNDAG</span>
				</th>
				<th>
					T<span className={"day-element"}>ISDAG</span>
				</th>
				<th>
					O<span className={"day-element"}>NSDAG</span>
				</th>
				<th>
					T<span className={"day-element"}>ORSDAG</span>
				</th>
				<th>
					F<span className={"day-element"}>REDAG</span>
				</th>
				<th>
					L<span className={"day-element"}>ÖRDAG</span>
				</th>
				<th>
					S<span className={"day-element"}>ÖNDAG</span>
				</th>
			</tr>
		</thead>
	);
};

export default TableThead;
