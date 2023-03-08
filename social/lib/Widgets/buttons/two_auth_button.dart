import 'package:flutter/material.dart';

import '../../constant.dart';

class TwoAuthButton extends StatelessWidget {
  const TwoAuthButton({Key? key, required this.image, required this.title})
      : super(key: key);
  final String image;
  final String title;
  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
          color: kMainColor1.withOpacity(0.5),
          borderRadius: BorderRadius.circular(30)),
      height: 55,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Image.asset(
            'assets/images/$image.png',
            height: 32,
            width: 32,
          ),
          const SizedBox(width: 6),
          Text(
            title,
            style: Theme.of(context).textTheme.bodyMedium,
          )
        ],
      ),
    );
  }
}
