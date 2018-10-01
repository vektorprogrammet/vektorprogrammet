async function getServers() {
  const response = await fetch("https://staging.vektorprogrammet.no/api/servers")
  return await response.json()
}

export const stagingService = {
  getServers,
};
