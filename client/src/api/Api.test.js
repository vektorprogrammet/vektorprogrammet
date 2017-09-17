import {apiEndpoint} from './Api';

it('provides localhost as default api endpoint', () => {
  expect(apiEndpoint).toEqual('http://localhost:8000/api')
});
