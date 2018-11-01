import { expect } from 'chai';
import { getters } from '../../src/store/account.module';


// const user

describe('Account store functions', () => {
  it('State', () => {
    console.log(getters);
    const state = {
      user: {
        loaded: false,
        loading: false,
      },
    };
    expect(getters.user(state)).toBe({ loading: false, loaded: true });
  });
});
