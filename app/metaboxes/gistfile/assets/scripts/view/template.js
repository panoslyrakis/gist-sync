import React from "react";
import Grid from "@mui/material/Grid";
import Header from "./header.js";
import Body from "./body.js";
import Footer from "./footer.js";

const MainTemplate = () => {
	return (
		<div>
			<Grid container spacing={3}>
				<Grid item xs={12}>
					<Header></Header>
				</Grid>
				<Grid item xs={12}>
					<Body></Body>
				</Grid>
				<Grid item xs={12}>
					<Footer></Footer>
				</Grid>
			</Grid>
		</div>
	);
};

export default MainTemplate;
