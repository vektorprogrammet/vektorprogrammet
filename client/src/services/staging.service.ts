import http from './http.service';

async function getServers() {
  const response = await http.get(
    'https://staging.vektorprogrammet.no/api/servers',
  );
  return await response.json();
}

async function getDiskSpace() {
  const response = await http.get(
    'https://staging.vektorprogrammet.no/api/disk-space',
  );
  return await response.json();
}

export const stagingService = {
  getServers,
  getDiskSpace,
};
