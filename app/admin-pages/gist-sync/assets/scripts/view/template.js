import React from 'react';
import Grid from '@mui/material/Grid';
import Header from './header.js'
import Body from './body.js'
import Footer from './footer.js'

const MainTemplate = () => {
	return (
		<div>
				<Grid container spacing={3}>
					<Grid>
						<Header></Header>
					</Grid>
					<Grid>
						<Body></Body>
					</Grid>
					<Grid>
						<Footer></Footer>
					</Grid>
				</Grid>
				
			</div>
	);
 }

export default MainTemplate