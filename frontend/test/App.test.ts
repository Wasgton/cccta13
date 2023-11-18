import {mount} from '@vue/test-utils';
import App from "../src/App.vue";

test("App.test.ts", async function () {
    const wrapper = mount(App,{});
    expect(wrapper.get(".signup-title").text()).toBe("SignUp");
    wrapper.get(".signup-name").setValue("John Doe");
    wrapper.get(".signup-email").setValue("email@email.com.br")
    wrapper.get(".signup-cpf").setValue("79878640051");
    wrapper.get(".signup-is-passenger").setValue(true);
    await wrapper.get(".signup-submit").trigger("click");
    expect(wrapper.get(".signup-account-id").text()).toBeDefined();
})