import 'package:flutter/material.dart';

import '../Widgets/buttons/two_auth_button.dart';
import '../Widgets/inputs.dart';
import '../constant.dart';

class LoginScreen extends StatelessWidget {
  const LoginScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Stack(
          children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: SingleChildScrollView(
                child: Column(
                  children: [
                    Padding(
                      padding: const EdgeInsets.only(top: 100),
                      child:
                          Center(child: Image.asset('assets/images/Logo.png')),
                    ),
                    const SizedBox(height: 30),
                    Text(
                      'USER LOGIN',
                      style: Theme.of(context).textTheme.titleLarge,
                    ),
                    const SizedBox(height: 40),
                    const FieldInput(hintText: 'UserName', image: 'User'),
                    const SizedBox(height: 25),
                    const FieldInput(hintText: 'Password', image: 'Unlock'),
                    const SizedBox(height: 32),
                    Container(
                      height: 55,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(30),
                        color: kMainColor2,
                        boxShadow: [
                          BoxShadow(
                            offset: const Offset(0, 4),
                            blurRadius: 4,
                            color: Colors.black.withOpacity(0.25),
                          ),
                        ],
                      ),
                      child: Center(
                        child: Text(
                          'LOGIN',
                          style: Theme.of(context).textTheme.bodyLarge,
                        ),
                      ),
                    ),
                    const SizedBox(height: 30),
                    Stack(
                      children: [
                        Divider(
                          color: Colors.black.withOpacity(0.5),
                        ),
                        Center(
                          child: Container(
                            color: Colors.white,
                            width: 30,
                            alignment: Alignment.center,
                            child: Text(
                              'or',
                              style: Theme.of(context).textTheme.bodySmall,
                            ),
                          ),
                        )
                      ],
                    ),
                    const SizedBox(height: 25),
                    const TwoAuthButton(
                      image: 'facebook',
                      title: 'Continue with facebook',
                    ),
                    const SizedBox(height: 12),
                    const TwoAuthButton(
                      image: 'google',
                      title: 'Continue with google',
                    ),
                    // Spacer(),
                  ],
                ),
              ),
            ),
            Column(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: 0),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        'Don\'t have an account, ',
                        style: TextStyle(color: Colors.black.withOpacity(0.5)),
                      ),
                      const Text('create one',
                          style: TextStyle(color: kMainColor2))
                    ],
                  ),
                ),
              ],
            )
          ],
        ),
      ),
    );
  }
}
