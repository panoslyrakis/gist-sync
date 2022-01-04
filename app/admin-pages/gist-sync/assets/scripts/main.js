import React, { useState } from 'react';
import Grid from '@mui/material/Grid';
import Box from '@mui/material/Box';
import MainTemplate from './view/template.js';

const GistSync = () => {

	return (
		<div>
			<h1>{gist_sync.labels.page_title}</h1>
			<Grid container spacing={3}>
				<Grid item xs={12}>
					<Box p={1} m={1}>
						<MainTemplate></MainTemplate>
					</Box>
				</Grid>
			</Grid>
		</div>
	);
 }

ReactDOM.render(
	<GistSync />,
	document.getElementById(gist_sync.data.unique_id)
);