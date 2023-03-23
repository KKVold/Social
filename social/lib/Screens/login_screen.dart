import 'package:flutter/material.dart';

import '../Widgets/buttons/two_auth_button.dart';
import '../Widgets/inputs.dart';
import '../constant.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: CustomScrollView(
          slivers: [
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Column(
                  children: [
                    Padding(
                      padding: const EdgeInsets.only(top: 100),
                      child:
                          Center(child: Image.asset('assets/images/Logo.png')),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 30),
                      child: Text(
                        'USER LOGIN',
                        style: Theme.of(context).textTheme.titleLarge,
                      ),
                    ),
                    const FieldInput(hintText: 'UserName', image: 'User'),
                    const Padding(
                      padding: EdgeInsets.symmetric(vertical: 30),
                      child: FieldInput(hintText: 'Password', image: 'Unlock'),
                    ),
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
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 25),
                      child: Stack(
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
                    ),
                    const TwoAuthButton(
                      image: 'facebook',
                      title: 'Continue with facebook',
                    ),
                    const SizedBox(height: 12),
                    const TwoAuthButton(
                      image: 'google',
                      title: 'Continue with google',
                    ),
                  ],
                ),
              ),
            ),
            SliverFillRemaining(
              hasScrollBody: false,
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 13),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          'Don\'t have an account, ',
                          style:
                              TextStyle(color: Colors.black.withOpacity(0.5)),
                        ),
                        const Text('create one',
                            style: TextStyle(color: kMainColor2))
                      ],
                    ),
                  ],
                ),
              ),
            )
          ],
        ),
      ),
    );
  }
}
