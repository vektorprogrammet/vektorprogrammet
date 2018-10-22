async function getServers() {
  const response = await fetch('https://staging.vektorprogrammet.no/api/servers');
  return await response.json();
}

async function getDiskSpace() {
  const response = await fetch('https://staging.vektorprogrammet.no/api/disk-space');
  return await response.json();
}

export const stagingService = {
  getServers,
  getDiskSpace,
};
