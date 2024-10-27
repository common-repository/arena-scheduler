import React from "react";
import ContentLoader from "react-content-loader";

const TableLoader = (props) => {
  // Define the number of rows and columns
  const numRows = 28;
  const numCols = 8;

  // Define the dimensions of each rectangle
  const rectWidth = 90;
  const rectHeight = 25;

  // Define the spacing between rectangles
  const rectSpacingX = 120;
  const rectSpacingY = 50;

  // Calculate the viewBox dimensions to include all rectangles with a 1-pixel margin
  const viewBoxWidth = 1 + numCols * rectSpacingX + 1;
  const viewBoxHeight = 1 + numRows * rectSpacingY + 1;

  // Function to create an array of rectangles based on the specified number of rows and columns
  const createRectangles = () => {
    const rectangles = [];
    for (let i = 0; i < numRows; i++) {
      for (let j = 0; j < numCols; j++) {
        // Calculate the x and y positions for each rectangle
        const x = 15 + j * rectSpacingX;
        const y = 12 + i * rectSpacingY;

        // Add a rectangle to the array with unique key and specified dimensions
        rectangles.push(
          <rect key={`${i}-${j}`} x={x} y={y} rx="10" ry="10" width={rectWidth} height={rectHeight} />
        );
      }
    }
    return rectangles;
  };

  // Return the ContentLoader component with the specified viewBox and styling
  return (
    <ContentLoader viewBox={`0 0 ${viewBoxWidth} ${viewBoxHeight}`} backgroundColor="#fff" foregroundColor="#999" {...props}>
      {createRectangles()}
    </ContentLoader>
  );
};

export default TableLoader;
