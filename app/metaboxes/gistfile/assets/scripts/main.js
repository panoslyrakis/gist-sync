import React, { useState } from "react";
import Grid from "@mui/material/Grid";
import Box from "@mui/material/Box";
import MainTemplate from "./view/template.js";

import Provider from "./components/Provider";

const FilelistMetabox = () => {
  return (
    <Provider>
      <h1>{gist_sync_files_metabox.labels.page_title}</h1>
      <Grid container spacing={3}>
        <Grid item xs={12}>
          <Box p={1} m={1}>
            <MainTemplate></MainTemplate>
          </Box>
        </Grid>
      </Grid>
    </Provider>
  );
};

ReactDOM.render(
  <FilelistMetabox />,
  document.getElementById(gist_sync_files_metabox.data.unique_id)
);
